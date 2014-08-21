<?php

namespace ZippyERP\ERP\Blocks;

use \Zippy\Binding\PropertyBinding as Prop;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\ArrayDataSource;
use \ZCL\DB\EntityDataSource as EDS;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\DropDownChoice;

/**
 * Виджет для  просмотра  документов 
 */
class DocView extends \Zippy\Html\PageFragment
{

    private $_doc;
    public $_reldocs = array();
    public $_entries = array();
    public $_statelist = array();
    public $_fileslist = array();
    public $_msglist = array();

    public function __construct($id)
    {
        parent::__construct($id);

        $this->add(new RedirectLink('print', ""));
        $this->add(new RedirectLink('pdf', ""));
        $this->add(new RedirectLink('word', ""));
        $this->add(new RedirectLink('excel', ""));
        $this->add(new Label('preview'));

        $this->add(new DataView('reldocs', new ArrayDataSource(new Prop($this, '_reldocs')), $this, 'relDoclistOnRow'));
        $this->add(new DataView('dw_entrylist', new ArrayDataSource(new Prop($this, '_entries')), $this, 'entryListOnRow'));
        $this->add(new DataView('dw_statelist', new ArrayDataSource(new Prop($this, '_statelist')), $this, 'stateListOnRow'));

        $this->add(new Form('addrelform'))->setSubmitHandler($this, 'OnReldocSubmit');
        $this->addrelform->add(new AutocompleteTextInput('addrel'))->setAutocompleteHandler($this, 'OnAddDoc');


        $this->add(new Form('addfileform'))->setSubmitHandler($this, 'OnFileSubmit');
        $this->addfileform->add(new \Zippy\Html\Form\File('addfile'));
        $this->addfileform->add(new TextInput('adddescfile'));
        $this->add(new DataView('dw_files', new ArrayDataSource(new Prop($this, '_fileslist')), $this, 'fileListOnRow'));

        $this->add(new Form('addmsgform'))->setSubmitHandler($this, 'OnMsgSubmit');
        $this->addmsgform->add(new TextArea('addmsg'));
        $this->add(new DataView('dw_msglist', new ArrayDataSource(new Prop($this, '_msglist')), $this, 'msgListOnRow'));

        $this->add(new Label('detuser'));
        $this->add(new Label('detcreated'));
        $this->add(new Label('detupdated'));
        $this->add(new Label('detnotes'));
    }

    // Устанавливаем  документ  для  просмотра
    public function setDoc(\ZippyERP\ERP\Entity\Doc\Document $item)
    {
        $this->_doc = $item;
        //  получение  екзамеляра  конкретного  документа   с  данными
        $type = Helper::getMetaType($item->type_id);
        $class = "\\ZippyERP\\ERP\\Entity\\Doc\\" . $type['meta_name'];
        $item = $class::load($item->document_id);

        // генерация  печатной   формы                
        $html = $item->generateReport();
        if (strlen($html) == 0) {
            $this->owner->setError("Не найден шаблон печатной формы");
            return;
        }

        $this->preview->setText($html, true);

        Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        $reportpage = "ZippyERP/ERP/Pages/ShowDoc";

        $filename = $type['meta_name'];

        $this->print->pagename = $reportpage;
        $this->print->params = array('print', $filename);
        $this->pdf->pagename = $reportpage;
        $this->pdf->params = array('pdf', $filename);
        $this->word->pagename = $reportpage;
        $this->word->params = array('doc', $filename);
        $this->excel->pagename = $reportpage;
        $this->excel->params = array('xls', $filename);

        $this->updateDocs();
        $this->_entries = \ZippyERP\ERP\Entity\Entry::find('document_id=' . $this->_doc->document_id);
        $this->dw_entrylist->Reload();
        $this->_statelist = $this->_doc->getLogList();
        $this->dw_statelist->Reload();

        $this->updateFiles();
        $this->updateMessages();

        $this->detuser->setText($this->_doc->userlogin);
        $this->detcreated->setText(date('Y-m-d H:i', $this->_doc->created));
        $this->detupdated->setText(date('Y-m-d H:i', $this->_doc->updated));
        $this->detnotes->setText($this->_doc->notes);
    }

    // обновление  списка  связанных  документов
    private function updateDocs()
    {
        $this->_reldocs = $this->_doc->ConnectedDocList();
        $this->reldocs->Reload();
    }

    //вывод строки  связанного  документа
    public function relDoclistOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new ClickLink('docitem'))->setClickHandler($this, 'detailDocOnClick');
        $row->add(new ClickLink('deldoc'))->setClickHandler($this, 'deleteDocOnClick');
        $row->docitem->setValue($item->meta_desc . ' ' . $item->document_number);
    }

    //удаление связанного  документа
    public function deleteDocOnClick($sender)
    {
        $doc = $sender->owner->getDataItem();
        $this->_doc->RemoveConnectedDoc($doc->document_id);
        $this->updateDocs();
    }

    //открыть связанный документ   
    public function detailDocOnClick($sender)
    {
        $id = $sender->owner->getDataItem()->document_id;
        App::Redirect('\ZippyERP\ERP\Pages\Register\DocList', $id);
    }

    //вывод строки  бухгалтерской проводки
    public function entryListOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('dt', $item->acc_d_code));
        $row->add(new Label('ct', $item->acc_c_code));
        $row->add(new Label('entryamount', number_format($item->amount / 100, 2)));
        $row->add(new Label('entrycomment', $item->comment));
    }

    //вывод строки  лога состояний
    public function stateListOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('statehost', $item->hostname));
        $row->add(new Label('statedate', $item->updatedon));
        $row->add(new Label('stateuser', $item->user));
        $row->add(new Label('statename', $item->state));
    }

    /**
     * добавление  связанного  документа
     * 
     * @param mixed $sender
     */
    public function OnReldocSubmit($sender)
    {

        $id = $this->addrelform->addrel->getKey();

        if ($id > 0) {
            $this->_doc->AddConnectedDoc($id);
            $this->updateDocs();
            $this->addrelform->addrel->setText('');
        } else {
            
        }
    }

    // автолоад списка  документов
    public function OnAddDoc($sender)
    {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select document_id,document_number from erp_document where document_number  like '%{$text}%' and document_id <> {$this->_doc->document_id} order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

    /**
     * добавление прикрепленного файла
     * 
     * @param mixed $sender
     */
    public function OnFileSubmit($sender)
    {

        $file = $this->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл более 10М !");
            return;
        }

        Helper::addFile($file, $this->_doc->document_id, $this->addfileform->adddescfile->getText(), \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_DOC);
        $this->addfileform->adddescfile->setText('');
        $this->updateFiles();
    }

    // обновление  списка  прикрепленных файлов
    private function updateFiles()
    {
        $this->_fileslist = Helper::getFileList($this->_doc->document_id, \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_DOC);
        $this->dw_files->Reload();
    }

    //вывод строки  прикрепленного файла
    public function filelistOnRow($row)
    {
        $item = $row->getDataItem();

        $file = $row->add(new \Zippy\Html\Link\BookmarkableLink("filename", _BASEURL . '?p=ZippyERP/ERP/Pages/LoadFile&arg=' . $item->file_id));
        $file->setValue($item->filename);
        $file->setAttribute('title', $item->description);

        $row->add(new ClickLink('delfile'))->setClickHandler($this, 'deleteFileOnClick');
    }

    //удаление прикрепленного файла
    public function deleteFileOnClick($sender)
    {
        $file = $sender->owner->getDataItem();
        Helper::deleteFile($file->file_id);
        $this->updateFiles();
    }

    /**
     * добавление коментария
     * 
     * @param mixed $sender
     */
    public function OnMsgSubmit($sender)
    {
        $msg = new \ZippyERP\ERP\Entity\Message();
        $msg->message = $this->addmsgform->addmsg->getText();
        $msg->created = time();
        $msg->user_id = System::getUser()->user_id;
        $msg->item_id = $this->_doc->document_id;
        $msg->item_type = \ZippyERP\ERP\Consts::MSG_ITEM_TYPE_DOC;
        if (strlen($msg->message) == 0)
            return;
        $msg->save();

        $this->addmsgform->addmsg->setText('');
        $this->updateMessages();
    }

    //список   комментариев
    private function updateMessages()
    {
        $this->_msglist = \ZippyERP\ERP\Entity\Message::find('item_type =1 and item_id=' . $this->_doc->document_id);
        $this->dw_msglist->Reload();
    }

    //вывод строки  коментария
    public function msgListOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", $item->message));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->userlogin));

        $row->add(new ClickLink('delmsg'))->setClickHandler($this, 'deleteMsgOnClick');
    }

    //удаление коментария
    public function deleteMsgOnClick($sender)
    {
        $msg = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Message::delete($msg->message_id);
        $this->updateMessages();
    }

}
