<?php
namespace Fhir;

interface MapToFhirInterface {
    public function mapToObservation($patient);    
    public function createPatienIfNotExists($patient);
}

?>