<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Helper as H;

/**
 * Страница  ввода возврата  на  склад
 */
class MoveBackItem extends \ZippyERP\ERP\Pages\Base
{
    private $_itemtype = array(201 => 'Материал', 281 => 'Товар', 22 => 'МПБ',26=>'Готовая продукция');

    public $_itemlist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date', time()));
        $this->docform->add(new DropDownChoice('storefrom'))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->add(new DropDownChoice('storeto'))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->storefrom->setOptionList(Store::findArray("storename", "store_type<>" . Store::STORE_TYPE_OPT));
        $this->docform->storeto->setOptionList(Store::findArray("storename", "store_type=" . Store::STORE_TYPE_OPT));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');


        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new AutocompleteTextInput('edititem'))->setAutocompleteHandler($this, 'OnAutocompleteItem');
        $this->editdetail->edititem->setChangeHandler($this, 'OnChangeItem');
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));


        $this->editdetail->add(new DropDownChoice('edittype', $this->_itemtype))->setChangeHandler($this,"OnItemType");


        $this->editdetail->add(new SubmitButton('saverow'))->setClickHandler($this, 'saverowOnClick');
        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->storefrom->setValue($this->_doc->headerdata['storefrom']);
            $this->docform->storeto->setValue($this->_doc->headerdata['storeto']);


            foreach ($this->_doc->detaildata as $item) {
                $stock = new Stock($item);
                $this->_itemlist[$stock->stock_id] = $stock;
            }
        } else {
            $this->_doc = Document::create('MoveBackItem');
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('item', $item->itemname));

        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity/1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        // unset($this->_itemlist[$item->item_id]);

        $this->_itemlist = array_diff_key($this->_itemlist, array($item->stock_id => $this->_itemlist[$item->stock_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        if ($this->docform->storefrom->getValue() == 0) {
            $this->setError("Выберите склад-источник");
            return;
        }
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->editdetail->edititem->setKey(0);

        $this->editdetail->edititem->setText('');
    }

    public function editOnClick($sender)
    {
        $stock = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($stock->quantity/1000);



        $this->editdetail->edititem->setKey($stock->stock_id);
        $this->editdetail->edititem->setText($stock->itemname);

        $this->editdetail->edittype->setValue($stock->type);
        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edititem->getKey();
        if ($id == 0) {
            $this->setError("Не выбран ТМЦ");
            return;
        }


        $stock = Stock::load($id);
        $stock->quantity = 1000*$this->editdetail->editquantity->getText();
        $stock->price = 100*$this->editdetail->editprice->getText();
        $stock->type = $this->editdetail->edittype->getValue();

        $doc =Document::getFirst("meta_name ='MoveItem' and  state=" . Document::STATE_EXECUTED. "  and content  like '%<item_id>{$stock->item_id}</item_id>%'");
        if ($doc == null) {
            $this->setWarn('Не найден документ перемещения со склада  с таким  ТМЦ');
        }

        $store = Store::load($this->docform->storefrom->getValue());
      // $fromstock = Stock::getStock($this->docform->storefrom->getValue(),$stock->item_id,$stock->price,false);
        $stockfrom = Stock::getFirst("store_id={$store->store_id} and item_id={$stock->item_id} and price={$stock->price} and partion={$stock->partion} and closed <> 1");

        if($stockfrom == null && $store->store_type == Store::STORE_TYPE_RET){
           $this->setError('Товар  с  такой  ценой и партией не найден  в  магазине');
           return;
        }


        $this->_itemlist[$stock->stock_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');
        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText("");

    }

    public function cancelrowOnClick($sender)
    {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');
        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText("");

    }

    public function savedocOnClick($sender)
    {
        if ($this->checkForm() == false) {
            return;
        }



        $this->_doc->headerdata = array(
            'storefrom' => $this->docform->storefrom->getValue(),
            'storeto' => $this->docform->storeto->getValue()
        );
        $this->_doc->detaildata = array();
        foreach ($this->_itemlist as $item) {
            $this->_doc->detaildata[] = $item->getData();
        }


        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->document_date->getText());
        $isEdited = $this->_doc->document_id > 0;

        $this->_doc->save();
        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        App::RedirectBack();
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm()
    {

        if (count($this->_itemlist) == 0) {
            $this->setError("Не введен ни один  товар");
            return false;
        }
        if ($this->docform->storeto->getValue() == $this->docform->storefrom->getValue()) {
            $this->setError("Выбран  тот  же  склад для  получения");
            return false;
        }


        return true;
    }

    public function backtolistOnClick($sender)
    {
        App::RedirectBack();
    }

    public function OnChangeItem($sender)
    {
        $stock_id = $sender->getKey();
        $stock = Stock::load($stock_id);
        $store = Store::load($this->docform->storeto->getValue());
        if ($store->store_type == Store::STORE_TYPE_OPT) {

        } else {
            $item = Item::load($stock->item_id);

        }
        if ($store->store_type == Store::STORE_TYPE_RET) {

        }
    }

    public function OnChangeStore($sender)
    {
        if ($sender->id == 'storefrom') {
            //очистка  списка  товаров
            $this->_itemlist = array();
            $this->docform->detail->Reload();
        }
        if ($sender->id == 'storeto') {


        }
    }

    public function OnItemType($sender){
     //   $stock_id = $this->editdetail->edititem->getKey();
   //     $stock = Stock::load($stock_id);
    //    if($stock==null) return;

     //  $this->editdetail->edititem->setKey(0);
     //   $this->editdetail->edititem->setText('');
    //    $this->editdetail->editquantity->setText("1");

    }
    public function OnAutocompleteItem($sender)
    {
        //ищем  партии  ТМЦ  на  оптовом складе
        $text = $sender->getValue();
        $store_id = $this->docform->storeto->getValue();

        return Stock::findArrayEx("store_id={$store_id} and closed <> 1 and  itemname  like '%{$text}%' and   stock_id in(select stock_id  from  erp_account_subconto  where  account_id= ".$this->editdetail->edittype->getValue() .") ");
    }


}
