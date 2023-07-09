<?php

/* fetchRecordsNoLog
fetchSingleValue
fetchRecords
sqlInsert */


require_once("../../../../globals.php");
require  'Model.php';
require '../../../../../src/FHIR/R4/FHIRDomainResource/FHIRObservation.php';
require '../../../../../src/RestControllers/FHIR/FhirObservationRestController.php';

use OpenEMR\RestControllers\FHIR\FhirObservationRestController;
use OpenEMR\FHIR\R4\FHIRElement\FHIRReference;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRObservation;
use OpenEMR\Modules\SmartDevice\PGHDFHIRResource;
$obsData = [
    "resourceType"=> "Observation",
    "status"=> "final",
    "code"=> [
      "coding"=> [
        [
          "system"=> "http=>//loinc.org",
          "code"=> "8867-4",
          "display"=> "Heart rate"
        ]      
    ]],
    "subject"=> [
      "reference"=> "Patient/123"
    ],
    "valueQuantity"=> [
      "value"=> 80,
      "unit"=> "bpm",
      "system"=> "http=>//unitsofmeasure.org",
      "code"=> "/min"
    ],
    "effectiveDateTime"=> "2022-03-11T10=>00=>00Z"
];  
$observation = new FHIRObservation($obsData);
$obsContainer = new FHIRResourceContainer($observation);
//$fhir = new FhirObservationRestController;
//$res = $fhir->post($obsData);
//echo  substr(strrchr(get_class($observation), 'FHIR'), 4);
var_dump($obsContainer);
if(isset($_POST['wearable'])){                
        require '../vendor/autoload.php';
              
        $wearable = $_POST['wearable'];
        $secrete_key = $_POST['secrete_key'];
        $client_id = $_POST['client_id'];
        $res =  Model::get("pghd_wearable",["wearable"=>$wearable],false);                      
        if(empty($res)){
            $res =  Model::insert("INSERT INTO `pghd_wearable`(`wearable`, `secrete_key`, `client_id`) VALUES (?,?,?) ",[$wearable,$secrete_key,$client_id]);                  
        }else{            
            $res =  Model::update("UPDATE `pghd_wearable` SET `secrete_key`=?, `client_id`=? WHERE id=$res->id ",[$secrete_key,$client_id]);                  
            echo $res;
        }        
    }
$wearables =  Model::get("pghd_wearable",[],true);        

?>
<html>
<title>Smart Device</title>
<?php
@include('nav.php');
?>
<div class="row w-100">
    <div class="col-xs-12 col-md-2 col-lg-1"></div>
    <div class="col-xs-12 col-md-8 col-lg-10 w-100 mx-0 pb-0 bg-white position-relative" id="app">
        <div class="accordion" id="faq">
            
            <div class="card">
                <div class="card-header bg-white" id="faqhead1">
                    <a href="#" class="btn btn-header-link" data-toggle="collapse" data-target="#faq1" aria-expanded="true" aria-controls="faq1">Register Wearable</a>
                </div>
                <div id="faq1" class="collapse show" aria-labelledby="faqhead1" data-parent="#faq">
                    <div class="card-body">                                            
                        <form method="post" action="">                        
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Wearable</span>
                                </div>
                                <select @change="onSelectWearable()" v-model="wearable" name="wearable" class="form-control">
                                    <option value="fitbit">Fitbit</option>
                                    <option value="googlefit">GoogleFit</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span  class="input-group-text" id="basic-addon1">Secrete Key</span>
                                </div>
                                <input v-model="secrete_key" type="text" name="secrete_key" class="form-control" placeholder="Wearable name" aria-label="name" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Client ID</span>
                                </div>
                                <input v-model="client_id" type="text" name="client_id" class="form-control" placeholder="https://" aria-label="name" aria-describedby="basic-addon1">
                            </div> 
                            <button class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
           <!--  <div class="card">
                <div class="card-header" id="faqhead2">
                    <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2" aria-expanded="true" aria-controls="faq2">S.S.S</a>
                </div>

                <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                    <div class="card-body">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="faqhead3">
                    <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3" aria-expanded="true" aria-controls="faq3">S.S.S</a>
                </div>

                <div id="faq3" class="collapse" aria-labelledby="faqhead3" data-parent="#faq">
                    <div class="card-body">
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    <div class="col-xs-12 col-md-2 col-lg-1"></div>
</div>
<script>
    const {
        createApp
    } = Vue
    createApp({
        data() {
            return {
                wearables: <?= json_encode($wearables) ?>,
                secrete_key:'',
                client_id:'',
                wearable:'',
            }
        },
        computed(){
            
        }, 
        created() {
            //axios.get('localhost')
        },
        methods: {           
            onSelectWearable(){
                const $this = this
                this.wearables.forEach(function(item,key){                         
                    if(item.wearable == $this.wearable){                        
                        $this.client_id = item.client_id
                        $this.secrete_key = item.secrete_key
                    }
                })
                return w;
            }
        },
        mounted() {

        }

    }).mount('#app')
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
        $('#tablexxxx').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'excel',
                'pdf'
            ],
            pageLength: 6,

        });
        setTimeout(function() {
            switchPage()
        }, 1000)
    })
</script>
<style>
    
#faq .card .card-header {
  border: 0;
  -webkit-box-shadow: 0 0 20px 0 rgba(213, 213, 213, 0.5);
          box-shadow: 0 0 20px 0 rgba(213, 213, 213, 0.5);
  border-radius: 2px;
  padding: 0;
}

#faq .card .card-header .btn-header-link {
  color: #fff;
  display: block;
  text-align: left;
  /* background: #FFE472; */
  color: #222;
  padding: 20px;
}

#faq .card .card-header .btn-header-link:after {
  content: "\f107";
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  float: right;
}

#faq .card .card-header .btn-header-link.collapsed {
  /* background: #A541BB; */
  /* color: #fff; */
}

#faq .card .card-header .btn-header-link.collapsed:after {
  content: "\f106";
}

#faq .card .collapsing {
  /* background: #FFE472; */
  line-height: 30px;
}

#faq .card .collapse {
  border: 0;
}

#faq .card .collapse.show {
  /* background: #FFE472; */
  line-height: 30px;
  color: #222;
}
</style>
</body>

</html>