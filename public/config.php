<?php
$patientId = $_SESSION['pid'] ?? 0;

$patientPortal = 0;
$patient = [];
if (isset($_SESSION['pid']) && isset($_SESSION['patient_portal_onsite_two'])) {
    $pid = $_SESSION['pid'];
    $patient = $pService->findByPid($pid);
    $patientPortal = 1;
}

?>