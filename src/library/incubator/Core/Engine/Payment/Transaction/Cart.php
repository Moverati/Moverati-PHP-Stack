<?php
namespace Core\Engine\Payment\Transaction;

class Cart extends \Core\Engine\Payment\Transaction {
    /**
     * @var array
     */
    protected $items = array();
    
    /**
     * @var \Core\Engine\Payment\Transaction\ItemInterface $item
     * @var integer $amount
     * @var double $tax
     * @return Cart
     */
    public function addItem(\Core\Engine\Payment\Transaction\ItemInterface $item, $qty = 1, $tax = 0.0) {
        $uid = $item->getPaymentTransactionUid();
        
        if( isset($this->items[$uid]) ) {
            $this->items[$uid]['qty'] += (int)$qty;
        }
        else {
            $this->items[$uid] = array(
                'item' => $item,
                'qty' => (int)$qty,
                'tax' => (double)$tax,
            );
        }
        
        return $this;
    }
    
    /**
     * @param \Core\Engine\Payment\Transaction\ItemInterface $item
     * @return Cart
     */
    public function removeItem(\Core\Engine\Payment\Transaction\ItemInterface $item) {
        unset($this->items[$item->getPaymentTransactionUid()]);
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getItems() {
        return $this->items;
    }
    
    /**
     * @return Cart
     */
    public function clearItems() {
        $this->items = array();
        
        return $this;
    }

    public function setSubTotal() {
        throw new \Core\Engine\Payment\Exception("Cannot manually set subtotal on a cart-based transaction");
    }
    
    /**
     * @return double
     */
    public function getSubTotal() {
        $subtotal = 0.0;
        foreach( $this->getItems() as $item ) {
            $subtotal += $item['item']->getPaymentTransactionAmount() * $item['qty'];
        }
        return $subtotal;
    }
}