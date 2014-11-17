<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Doc\BankStatement as BS;
use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Helper as H;

/**
 * Банковская   выписка 
 */
class BankStatement extends \ZippyERP\ERP\Pages\Base
{

    public $_list = array();
    private $_doc;

    public function __construct($docid = 0)
    {
        parent::__construct();
        $this->add(new Form('docform'));
        $this->docform->add(new Date('created', time()));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new TextInput('notes'));
        $this->docform->add(new DropDownChoice('bankaccount', \ZippyERP\ERP\Entity\MoneyFund::findArray('title', "ftype=1")));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new DropDownChoice('editoptype', BS::getTypes()))->setChangeHandler($this, 'typeOnClick');
        $this->editdetail->add(new DropDownChoice('editcustomer'));
        $this->editdetail->add(new DropDownChoice('editpayment'))->setOptionList(\ZippyERP\ERP\Consts::getTaxesList());
        $this->editdetail->editpayment->setVisible(false);
        $docinput = $this->editdetail->add(new AutocompleteTextInput('editdoc'));
        $docinput->setAutocompleteHandler($this, 'OnDocAutocomplete');
        $docinput->setAjaxChangeHandler($this, 'OnDocChange');
        $this->editdetail->add(new TextInput('editamount'))->setText("1");
        $this->editdetail->add(new TextInput('editnds'))->setText("0");
        $this->editdetail->add(new TextInput('editcomment'));
        $this->editdetail->add(new CheckBox('editnoentry'));
        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->setClickHandler($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);

            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->notes->setText($this->_doc->notes);
            $this->docform->bankaccount->setValue($this->_doc->headerdata['bankaccount']);
            $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));


            foreach ($this->_doc->detaildata as $item) {
                $entry = new Entry($item);
                $this->_list[$entry->entry_id] = $entry;
            }
            $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));
        } else {
            $this->_doc = Document::create('BankStatement');
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_list')), $this, 'detailOnRow'));

        $this->docform->detail->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();
        $types = BS::getTypes();
        $row->add(new Label('optype', $types[$item->optype]));
        $row->add(new Label('customer', $item->customername));
        $row->add(new Label('amount', H::fm($item->amount)));
        $row->add(new Label('document', $item->docnumber));
        $row->add(new Label('comment', $item->comment));
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        $entry = $sender->owner->getDataItem();
        // unset($this->_entrylist[$tovar->tovar_id]);

        $this->_list = array_diff_key($this->_list, array($entry->entry_id => $this->_list[$entry->entry_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
    }

    public function saverowOnClick($sender)
    {

        $doc = $this->editdetail->editdoc->getKey();
        if ($doc == 0) {
            $this->setError("Не выбран документ  или счет");
            return;
        }


        $entry = new Entry();   //используем   класс  проводки  для   строки
        $entry->optype = $this->editdetail->editoptype->getValue();


        $entry->doc = $this->editdetail->editdoc->getKey();
        $entry->docnumber = $this->editdetail->editdoc->getValue();
        $entry->customer = $this->editdetail->editcustomer->getValue();
        if ($entry->customer > 0) {
            $list = $this->editdetail->editcustomer->getOptionList();
            $entry->customername = $list[$entry->customer];
        }
        $entry->amount = $this->editdetail->editamount->getText() * 100;
        $entry->nds = $this->editdetail->editnds->getText() * 100;
        $entry->tax = $this->editdetail->editpayment->getValue();
        $entry->comment = $this->editdetail->editcomment->getText();
        $entry->noentry = $this->editdetail->editnoentry->isChecked();
        $entry->entry_id = time();
        $this->_list[$entry->entry_id] = $entry;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->editoptype->setValue(0);
        $this->editdetail->editpayment->setValue(0);
        $this->editdetail->editdoc->setKey(0);
        $this->editdetail->editdoc->setText('');
        $this->editdetail->editcustomer->setOptionList(array());
        $this->editdetail->editamount->setText("0");
        $this->editdetail->editnds->setText("0");
        $this->editdetail->editcomment->setText("");
    }

    public function cancelrowOnClick($sender)
    {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender)
    {
        if ($this->checkForm() == false) {
            return;
        }


        $this->_doc->detaildata = array();
        $total = 0;
        foreach ($this->_list as $entry) {
            $this->_doc->detaildata[] = $entry->getData();
            $total += $entry->amount;
        }

        $this->_doc->amount = $total;
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->notes = $this->docform->notes->getText();
        $this->_doc->headerdata['bankaccount'] = $this->docform->bankaccount->getValue();
        $isEdited = $this->_doc->document_id > 0;

        $this->_doc->save();

        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }

        App::Redirect('\ZippyERP\ERP\Pages\Register\DocList', $this->_doc->document_id);
    }

    /**
     * Валидация   формы
     * 
     */
    private function checkForm()
    {

        if (count($this->_list) == 0) {
            $this->setError("Не введена ни одна строка");
            return false;
        }
        /*  if (strlen($this->docform->basedoc->getValue()) > 0) {
          if ($this->docform->basedoc->getKey() > 0) {

          } else {
          $this->setError("Неверно введен документ-основание");
          return false;
          }
          } */
        return true;
    }

    public function backtolistOnClick($sender)
    {
        App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
    }

    public function typeOnClick($sender)
    {
        $this->editdetail->editnds->setVisible(true);
        $this->editdetail->editdoc->setVisible(true);
        $list = array();
        if ($sender->getValue() == BS::IN) {
            $list = Customer::getBuyers();  //если  приход то  продавцы
        }
        if ($sender->getValue() == BS::OUT) {
            $list = Customer::getSellers();  //если  расход  то  покупатели
        }
        if ($sender->getValue() == BS::TAX) {
            $list = Customer::getGov();  // оплата  налогов  и  сборов
            $this->editdetail->editnds->setVisible(false);
            $this->editdetail->editdoc->setVisible(false);
            $this->editdetail->editpayment->setVisible(true);
        } else {
            $this->editdetail->editpayment->setVisible(false);
        }
        if ($sender->getValue() == BS::CASHIN || $sender->getValue() == BS::CASHOUT) {
            $this->editdetail->editnds->setVisible(false);
            $this->editdetail->editdoc->setVisible(false);
            $this->editdetail->editcustomer->setVisible(false);
        } else {
            $this->editdetail->editcustomer->setVisible(true);
        }
        $this->editdetail->editcustomer->setOptionList($list);
    }

    public function OnDocAutocomplete($sender)
    {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select document_id,document_number from erp_document where document_number  like '%{$text}%' and document_id <> {$this->_doc->document_id} and state <> " . Document::STATE_EXECUTED . "  order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

    //выбран документ
    public function OnDocChange($sender)
    {
        $id = $sender->getKey();
        $doc = Document::load($id);
        if ($doc instanceof Document) {
            $this->editdetail->editamount->setText(H::fm($doc->amount));
            $this->editdetail->editnds->setText(H::fm($doc->headerdata['nds']));
        }
        $this->updateAjax(array('editnds', 'editamount'));
    }

}
