<?php

namespace ZippyERP\ERP\Pages\Doc;

use ZippyERP\System\Application as App;
use ZippyERP\System\System;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Helper as H;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\Date;
use Zippy\Html\Link\SubmitLink;

/**
 * Страница  документа финансовые результаты
 *
 */
class FinResult extends \ZippyERP\ERP\Pages\Base
{

    private $_doc;

    public function __construct($docid = 0)
    {
        parent::__construct();
        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('created'))->setDate(time());
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');
        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            if ($this->_doc == null)
                App::RedirectError('Докумен не найден');
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->created->setDate($this->_doc->document_date);
        } else {
            $this->_doc = Document::create('FinResult');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }
    }

    public function savedocOnClick($sender)
    {


        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $isEdited = $this->_doc->document_id > 0;

        $conn = \ZCL\DB\DB::getConnect();
        $conn->BeginTrans();
        try {
            $this->_doc->save();
            if ($sender->id == 'execdoc') {
                $this->_doc->updateStatus(Document::STATE_EXECUTED);
            } else {
                $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
            }

            $conn->CommitTrans();
            App::RedirectBack();
        } catch (\ZippyERP\System\Exception $ee) {
            $conn->RollbackTrans();
            $this->setError($ee->message);
        } catch (\Exception $ee) {
            $conn->RollbackTrans();
            throw new \Exception($ee->message);
        }
    }

    public function backtolistOnClick($sender)
    {
        App::RedirectBack();
    }

}
