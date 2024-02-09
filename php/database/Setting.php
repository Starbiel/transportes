<?php 
namespace php\database;

class Setting {
    public float $gasPrice = 4.50;
    public float $driverCommission = 0.16;
    public float $truckCommission = 0.10;
    private float $driverPayment = 0;
    private float $truckPart = 0;
    private float $dieselClan = 4.5;
    private float $dieselNormal = 5.4;
    private float $toll = 0;
    private float $diesel = 0;
    private float $vale = 0;

    public function driverPaymentCalc($sum, $extras = NULL) {
        $this->driverPayment = ($sum*$this->driverCommission);
        if($extras != NULL) {
            foreach ($extras as $key => $value) {
                if($value[1] == 'vale') {
                    $value[0] *= -1;
                    $this->vale += $value[0]; 
                }
                else if($value[1] == 'literDiesel') {
                    $this->diesel += $value[0];
                    $value[0] *= $this->dieselNormal;
                }
                else if($value[1] == 'toll') {
                    $this->toll += $value[0]; 
                }
               $this->driverPayment += $value[0];
            }
        }
        return [
            "driverPayment" => $this->driverPayment,
            "vales" => $this->vale,
            "dieselLiters" => $this->diesel,
            "toll" => $this->toll];
    }

    public function truckerPartCalc($sum, $extras = NULL) {
        $this->truckPart = $sum*$this->truckCommission;
        if($extras != NULL) {
            foreach ($extras as $key => $value) {
                $this->truckPart += $value[0];
            }
        }
        return $this->truckPart;
    }
}
?>