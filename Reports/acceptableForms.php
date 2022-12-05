<?php 

// Please test each form.


// has vehicle, unknown offender, unknown owner;
$acceptableForm1 = ["reportType"=>"known vehicle only",
"format"=>["reportStatement"=>"true",
    "reportDate"=>"true",
    "reportOffence"=>"true",
    "vehicleLicence"=>"true",
    "vehicleColour"=>"true",
    "vehicleMake"=>"true",
    "vehicleModel"=>"true",
    "ownerLicence"=>"false",
    "ownerFirstName"=>"false",
    "ownerLastName"=>"false",
    "ownerAddress"=>"false",
    "ownerDOB"=>"false",
    "offenderLicence"=>"false",
    "offenderFirstName"=>"false",
    "offenderLastName"=>"false",
    "offenderAddress"=>"false",
    "offenderDOB"=>"false"]
]; 

// has vehicle, known offender, unknown owner;
$acceptableForm2 = ["reportType"=>"known vehicle and offender",
"format"=>["reportStatement"=>"true",
    "reportDate"=>"true",
    "reportOffence"=>"true",
    "vehicleLicence"=>"true",
    "vehicleColour"=>"true",
    "vehicleMake"=>"true",
    "vehicleModel"=>"true",
    "ownerLicence"=>"false",
    "ownerFirstName"=>"false",
    "ownerLastName"=>"false",
    "ownerAddress"=>"false",
    "ownerDOB"=>"false",
    "offenderLicence"=>"optional",
    "offenderFirstName"=>"true",
    "offenderLastName"=>"true",
    "offenderAddress"=>"true",
    "offenderDOB"=>"true"]
]; 

// has vehicle, known offender, known owner;
$acceptableForm3 = ["reportType"=>"known vehicle and owner and offender",
"format"=>["reportStatement"=>"true",
    "reportDate"=>"true",
    "reportOffence"=>"true",
    "vehicleLicence"=>"true",
    "vehicleColour"=>"true",
    "vehicleMake"=>"true",
    "vehicleModel"=>"true",
    "ownerLicence"=>"optional",
    "ownerFirstName"=>"true",
    "ownerLastName"=>"true",
    "ownerAddress"=>"true",
    "ownerDOB"=>"true",
    "offenderLicence"=>"optional",
    "offenderFirstName"=>"true",
    "offenderLastName"=>"true",
    "offenderAddress"=>"true",
    "offenderDOB"=>"true"]
]; 

// has vehicle, unknown offender, known owner;
$acceptableForm4 = ["reportType"=>"known vehicle and owner",
"format"=>["reportStatement"=>"true",
    "reportDate"=>"true",
    "reportOffence"=>"true",
    "vehicleLicence"=>"true",
    "vehicleColour"=>"true",
    "vehicleMake"=>"true",
    "vehicleModel"=>"true",
    "ownerLicence"=>"optional",
    "ownerFirstName"=>"true",
    "ownerLastName"=>"true",
    "ownerAddress"=>"true",
    "ownerDOB"=>"true",
    "offenderLicence"=>"false",
    "offenderFirstName"=>"false",
    "offenderLastName"=>"false",
    "offenderAddress"=>"false",
    "offenderDOB"=>"false"]
]; 

// has no vehicle, known offender, unknown owner;
$acceptableForm5 = ["reportType"=>"known offender only",
"format"=>["reportStatement"=>"true",
    "reportDate"=>"true",
    "reportOffence"=>"true",
    "vehicleLicence"=>"false",
    "vehicleColour"=>"false",
    "vehicleMake"=>"false",
    "vehicleModel"=>"false",
    "ownerLicence"=>"false",
    "ownerFirstName"=>"false",
    "ownerLastName"=>"false",
    "ownerAddress"=>"false",
    "ownerDOB"=>"false",
    "offenderLicence"=>"optional",
    "offenderFirstName"=>"true",
    "offenderLastName"=>"true",
    "offenderAddress"=>"true",
    "offenderDOB"=>"true"]
]; 

?>