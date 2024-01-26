<?php 
namespace php\database;

class Setting {
    public float $gasPrice = 4.50;
    public float $driverCommission = 0.16;
    public float $truckCommission = 0.10;
    private float $driverPayment = 0;
    private float $truckPart = 0;
    
    public function driverPaymentCalc($sum, $extras = NULL) {
        $this->driverPayment = ($sum*$this->driverCommission);
        if($extras != NULL) {
            foreach ($extras as $key => $value) {
               $this->driverPayment += $value;
            }
        }
        return $this->driverPayment;
    }

    public function truckerPartCalc($sum) {
        $this->truckPart = $sum*$this->truckCommission;
        return $this->truckPart;
    }
}
?>