<?php
/* fetchRecordsNoLog
fetchSingleValue
fetchRecords
sqlInsert */
require_once("../../../../globals.php");
require  'Model.php';
session_start();
include('config.php');
use OpenEMR\Services\PatientService;
$pService = new PatientService;
$patients = [];
if($pid == 0){
    $patients = $pService->getAll()->getData();
}
/* $keys = array_keys(get_object_vars($patients));
print_r($keys); */
//var_dump($patients);
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
//$patientId = $_SESSION['pid'];
//$patientId = $_SESSION['pid'];
$_SESSION['wearables'] = $wearables;
?>
<html>
<title>Smart Device</title>
<?php
@include('nav.php');
?>
<div class="row w-100">
    <div class="col-xs-12 col-md-2 col-lg-1"></div>
    <div class="col-xs-12 col-md-8 col-lg-10 w-100 mx-0 pb-0 pt-3 bg-white position-relative shadow-sm " id="app" style="height:75vh; border-bottom:3px solid #ccc;">
        <div v-if="!processing" style="height: inherit;">     
            <div class="col-md-4" v-if="!patientPortal">                
                <select  v-model="patient_id" class="form-control selectpicker">
                    <option value=""></option>
                    <option v-for="patient in patients" :value="patient.uuid">{{patient.fname}} {{patient.lname}}</option>
                </select>
            </div>   
            <center>
                <div class="mt-5 p-4 rounded  d-flex flex-column align-items-center justify-center w-75">
                    <div v-for="wearable in wearables">
                        <a v-if="wearable.secrete_key !=''" @click="process(wearable.wearable)" :href="'authorized.php?wtype='+wearable.wearable+'&pid='+patient_id" target="_blank"  class="my-2 d-flex align-items-center btn btn-light px-3 py-2" style="width: 200px; height:60px">
                            <img style="width:100%" :src="'assets/images/'+wearable.wearable+'.png'" class="d-inline-block mr-2">                    
                        </a>
                        <a v-else  target="_blank"  class="my-2 d-flex align-items-center btn btn-light px-3 py-2 disabled" style="width: 200px; height:60px">
                            <img style="width:100%" :src="'assets/images/'+wearable.wearable+'.png'" class="d-inline-block mr-2">                    
                        </a>
                    </div>        
                </div>
            </center>
        </div>
        <div v-else>
            <div v-if="!completed" id="heartLoader" class="w-100 d-flex flex-column justify-content-center align-items-center" style="height:100%">
                <div class="spinner-border" role="status" style="width: 60px;height:60px">
                </div>
                Waiting for authorization completion...
            </div>
            <div v-else>
                <h5 class="mb-4 text-secondary">Import and Map Data</h5>
                <label>Date to Fetch Data From:</label>
                <input v-model="startDate" type="date" class="form-control mb-3">
                <label>Date to Fetch Data To:</label>
                <input v-model="endDate" type="date" class="form-control mb-3">
                <label>Data Type to Fetch:</label>
                <select v-model="type" class="form-control mb-3">
                    <option value="heartrate">Heart Rate</option>
                    <option value="sleep">Sleep</option>
                </select>
                <button @click="importAndMap()"  class="btn btn-secondary text-white">Sync Data</button>
            </div>
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
                patients:<?= json_encode($patients) ?>,
                patient: '<?= json_encode($patient) ?>',
                patient_id: '<?= $pid ?>',
                processing:false,
                completed:false,
                pghd_tokens:null,
                type:"",
                startDate:'',
                endDate:<?= date('Y-m-d')?>,
            }
        },
        computed(){
            
        }, 
        created() {
            //console.log(this.patients)
            //axios.get('localhost')
        },
        methods: {  
            process(name){
                this.processing = true;
                this.completed = false;
                this.wearable = name;
                this.checkForAuthorizationCompletion(name)

            },
            baseUrl() {
                let path = window.location.pathname.split('public')[0];
                let baseUrl = window.location.origin + path + 'public';
                return baseUrl;
            },
            async checkForAuthorizationCompletion(name){                            
                let response = await axios.get(`${this.baseUrl()}/check_for_authorzation_completion.php?pid=${this.patient_id}&name=${name}`);
                if (response.status == 200) {
                    if(response.data){
                        this.pghd_tokens = JSON.parse( JSON.stringify(response.data));                                                           
                        this.completed = true
                    }else{
                        alert('somthing went wrong; try again')
                    }
                }                
            }, 
            async importAndMap(){                            
                if(this.type ==''){
                    alert('select an option')
                    return false;
                }
                console.log(this.pghd_tokens, this.pghd_tokens.id)
                
                let response = await axios.get(`${this.baseUrl()}/import_and_map.php?pid=${this.patient_id}&id=${this.pghd_tokens.id}&type=${this.type}&startDate=${this.startDate}&endDate=${this.endDate}&wearable=${this.wearable}`);
                if (response.status == 200) {
                    if(response.data){
                 
                    }else{
                        alert('somthing went wrong; try again')
                    }
                }                
            },                     
            onSelectWearable(){
                const $this = this
                this.wearables.forEach(function(item,key){                         
                    if(item.wearable == $this.wearable){
                        $this.client_id = item.client_id
                        $this.secrete_key = item.secrete_key
                    }
                })                
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