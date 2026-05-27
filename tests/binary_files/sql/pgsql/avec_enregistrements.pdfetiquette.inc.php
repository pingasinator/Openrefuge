<?php


$champs_compteur = array(
    1, // flag
    85, // offset_x
    0, // offset_y
    10, // width
    0, // override_bold
    8, // override_size
    10, // XXX
    "R" // XXX
);

$img = array(
    "0" => array(
        "../lib/om-assets/img/arrow-right-16.png",
        1, 10, 0, 0, 'png'
    ),
    "1" => array(
        "../lib/om-assets/img/arrow-right-16.png",
        20, 20, 0, 0, 'png'
    ),
);

//
$texte = array(
    // "0" => array(
    //     //"Yo", // content
    //     // 10, // offset_x
    //     // 25, // offset_y
    //     // 10, // width
    //     // "", // override_bold
    //     // "", // override_size
    // ),
    "1" => array(
        "Ya", // content
        0, // offset_x
        25, // offset_y
        10, // width
        "", // override_bold
        "", // override_size
    ),
);

//
$champs = array(
    //
    "elem1" => array(
        "a", // content_prefix
        "a", // content_suffix
        array(
            0, // offset_x
            0, // offset_y
            50, // width
            "", // override_bold
            "", // override_size
        ),
        "", //
    ),
    //
    "elem2" => array(
        "", // content_prefix
        "", // content_suffix
        array(
            0, // offset_x
            5, // offset_y
            50, // width
            1, // override_bold
            "", // override_size
        ),
        "", //
    ),
    //
    "elem3" => array(
        "", // content_prefix
        "", // content_suffix
        array(
            0, // offset_x
            10, // offset_y
            50, // width
            "", // override_bold
            "", // override_size
        ),
        1, //
    ),
    //
    "elem4" => array(
        "", // content_prefix
        "", // content_suffix
        array(
            0, // offset_x
            15, // offset_y
            50, // width
            "", // override_bold
            "", // override_size
        ),
        0, // int - 0: string - 1: float
    ),
);

//
$sql = "
SELECT
    om_droit.om_droit AS elem1,
    om_droit.libelle AS elem2,
    om_droit.om_profil as elem3,
    om_profil.libelle as elem4
FROM ".DB_PREFIXE."om_droit
    LEFT JOIN ".DB_PREFIXE."om_profil
        ON om_droit.om_profil=om_profil.om_profil
ORDER BY
    om_droit.libelle
";
