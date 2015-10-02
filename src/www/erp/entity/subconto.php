<?php

namespace ZippyERP\ERP\Entity;

/**
 * сущность для хранения аналитического  усчета
 *
 * @table=erp_account_subconto
 * @keyfield=subconto_id
 */
class SubConto extends \ZCL\DB\Entity
{

    /**
     *
     *
     * @param mixed $document    Ссылка  на  документ
     * @param mixed $account_id  Синтетический  счет
     * @param mixed $amount      Сумма. Отрицательная если  счет  идет по  кредиту
     */
    public function __construct($document, $account_id, $amount)
    {
        parent::__construct();

        if ($document instanceof \ZippyERP\ERP\Entity\Doc\Document) {
            $this->document_id = $document->document_id;
            $this->document_date = $document->document_date;
        } else {
            throw new \ZippyERP\System\Exception("Не задан документ для субконто");
        }
        if ($account_id > 0) {
            $this->account_id = $account_id;
        } else {
            throw new \ZippyERP\System\Exception("Не задан счет для субконто");
        }
        if ($account_id > 0) {
            $this->amount = $amount;
        } else {
            throw new \ZippyERP\System\Exception("Не задана ссумма для субконто");
        }
    }

    protected function afterLoad()
    {
        $this->document_date = strtotime($this->document_date);
    }

    public function setStock($stock_id)
    {
        $this->stock_id = $stock_id;
    }

    public function setEmployee($employee_id)
    {
        $this->employee_id = $employee_id;
    }

    public function setCustomer($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function setMoneyfund($moneyfund_id)
    {
        $this->moneyfund_id = $moneyfund_id;
    }

    //типы  налогов, начислений  удержаний, прочая вспомагтельная  аналитика
    public function setExtCode($code)
    {
        $this->extcode = $code;
    }

    //отрицательное  если  счет по  кредиту
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Получение  количества   по  комбинации измерений
     *
     * @param mixed $date       дата на  конец дня
     * @param mixed $acc        синтетичкеский счет
     * @param mixed $stock      товар (партия)
     * @param mixed $customer   контрашент
     * @param mixed $emp        сотрудник
     * @param mixed $mf         денежный счет
     * @param mixed $code       универсальное поле
     */
    public static function getQuantity($date, $acc = 0, $stock = 0, $customer = 0, $emp = 0, $mf = 0, $code = 0)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $where = "   date(document_date) <= " . $conn->DBDate($date);
        if ($acc > 0) {
            $where = $where . " and account_id= " . $acc;
        }
        if ($emp > 0) {
            $where = $where . " and employee_id= " . $emp;
        }
        if ($mf > 0) {
            $where = $where . " and moneyfund_id= " . $mf;
        }
        if ($code > 0) {
            $where = $where . " and extcode= " . $code;
        }

        if ($stock > 0) {
            $where = $where . " and stock_id= " . $store;
        }
        if ($customer > 0) {
            $where = $where . " and customer_id= " . $customer;
        }
        $sql = " select coalesce(sum(quantity),0) AS quantity  from erp_account_subconto  where " . $where;
        return $conn->GetOne($sql);
    }

    /**
     * Получение  суммы   по  комбинации измерений
     *
     * @param mixed $date       дата на  конец дня
     * @param mixed $acc        синтетичкеский счет
     * @param mixed $stock      товар (партия)
     * @param mixed $customer   контрашент
     * @param mixed $emp        сотрудник
     * @param mixed $mf         денежный счет
     * @param mixed $code       универсальное поле
     */
    public static function getAmount($date, $acc = 0, $stock = 0, $customer = 0, $emp = 0, $mf = 0, $code = 0)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $where = "   date(document_date) <= " . $conn->DBDate($date);
        if ($acc > 0) {
            $where = $where . " and account_id= " . $acc;
        }
        if ($emp > 0) {
            $where = $where . " and employee_id= " . $emp;
        }
        if ($mf > 0) {
            $where = $where . " and moneyfund_id= " . $mf;
        }
        if ($code > 0) {
            $where = $where . " and extcode= " . $code;
        }

        if ($stock > 0) {
            $where = $where . " and stock_id= " . $store;
        }
        if ($customer > 0) {
            $where = $where . " and customer_id= " . $customer;
        }
        $sql = " select coalesce(sum(amount),0) AS quantity  from erp_account_subconto  where " . $where;
        return $conn->GetOne($sql);
    }

}
