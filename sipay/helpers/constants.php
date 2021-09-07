<?php

const SIPAY_SANDBOX_URL = "https://sandbox.sipay.es";
const SIPAY_LIVE_URL    = "https://live.sipay.es";
const SIPAY_DEVELOP_URL = "https://develop.sipay.es";

CONST SDK_JS_URL      = "https://cdn.jsdelivr.net/gh/Sipay/fpay-sdk-javascript@1.0/build/pwall-sdk.min.js";

const SIPAY_ENVIROMENTS_URLS = [
  "sandbox" => SIPAY_SANDBOX_URL,
  "live"    => SIPAY_LIVE_URL,
  "develop" => SIPAY_DEVELOP_URL
];


const CHECKOUT_TAG_VIRTUAL      = "digital";
const CHECKOUT_TAG_NOVIRTUAL    = "fisico";
const CHECKOUT_TAG_BOTH         = "mixto";

const EXPRESS_CHECKOUT_TAG      = "express";

CONST POSTCODE_REGIONID_SPAIN = [
    "01"	=> "VI",
    "02"	=> "AB",
    "03"	=> "A",
    "04"	=> "AL",
    "05"	=> "AV",
    "06"	=> "BA",
    "07"	=> "PM",
    "08"	=> "B",
    "09"	=> "BU",
    "10"	=> "CC",
    "11"	=> "CA",
    "12"	=> "CS",
    "13"	=> "CR",
    "14"	=> "CO",
    "15"	=> "C",
    "16"	=> "CU",
    "17"	=> "GI",
    "18"	=> "GR",
    "19"	=> "GU",
    "20"	=> "SS",
    "21"	=> "H",
    "22"	=> "HU",
    "23"	=> "J",
    "24"	=> "LE",
    "25"	=> "L",
    "26"	=> "LO",
    "27"	=> "LU",
    "28"	=> "M",
    "29"	=> "MA",
    "30"	=> "MU",
    "31"	=> "NA",
    "32"	=> "OR",
    "33"	=> "O",
    "34"	=> "P",
    "35"	=> "GC",
    "36"	=> "PO",
    "37"	=> "SA",
    "38"	=> "TF",
    "39"	=> "S",
    "40"	=> "SG",
    "41"	=> "SE",
    "42"	=> "SO",
    "43"	=> "T",
    "44"	=> "TE",
    "45"	=> "TO",
    "46"	=> "V",
    "47"	=> "VA",
    "48"	=> "BI",
    "49"	=> "ZA",
    "50"	=> "Z",
    "51"	=> "CE",
    "52"	=> "ML",
  ];
