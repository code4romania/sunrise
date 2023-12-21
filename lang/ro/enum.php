<?php

declare(strict_types=1);

return [

    'civil_status' => [
        'single' => 'Necăsătorit(ă)',
        'married' => 'Căsătorit(ă)',
        'divorced' => 'Divorțat(ă)',
        'widowed' => 'Văduv(ă)',
        'cohabitation' => 'Uniune liberă (concubinaj)',
    ],

    'gender' => [
        'male' => 'Masculin',
        'female' => 'Feminin',
        'other' => 'Altul',
    ],

    'id_type' => [
        'birth_certificate' => 'Certificat de naștere',
        'id_card' => 'Carte de identitate',
        'national_passport' => 'Pașaport Românesc',
        'foreign_passport' => 'Pașaport Străin',
        'other' => 'Alt act de identitate',
        'none' => 'Nu deține act de identitate',
    ],

    'residence_environment' => [
        'urban' => 'Urban',
        'rural' => 'Rural',
        'unknown' => 'Necunoscut',
    ],

    'studies' => [
        'none' => 'Fără studii',
        'primary' => 'Ciclu primar (I-IV)',
        'secondary' => 'Ciclu gimnazial (V-VIII)',
        'vocational' => 'Școală profesională de arte și meserii',
        'highschool' => 'Liceu (IX-XII)',
        'postsecondary' => 'Școală postliceală',
        'highereducation' => 'Studii superioare',
    ],

    'occupation' => [
        'none' => 'Fără ocupație',
        'employee' => 'Salariat',
        'self_employed' => 'Lucrător pe cont propriu',
        'association_worker' => 'Lucrător în asociație',
        'business_owner' => 'Patron',
        'farmer' => 'Agricultor',
        'student' => 'Elev/Student',
        'unemployed' => 'Șomer',
        'domestic' => 'Casnic',
        'retired' => 'Pensionar',
    ],

    'income' => [
        'none' => 'Fără venit',
        'below_minimum' => 'Sub salariul minim pe economie',
        'between_minimum_average' => 'Între salariul minim și salariul mediu pe economie',
        'above_average' => 'Peste salariul mediu pe economie',
        'social_benefits' => 'Prestații sociale',
        'other' => 'Alte situații (Alocații de stat)',
    ],

    'homeownership' => [
        'none' => 'Fără locuință',
        'property_of_victim' => 'Proprietate victimă',
        'property_of_aggressor' => 'Proprietate agresor',
        'joint' => 'Coproprietate',
        'family_of_victim' => 'La familia de origine a victimei',
        'family_of_aggressor' => 'La familia de origine a agresorului',
        'rent' => 'Închiriată',
        'other' => 'Altă situație',
    ],

    'ternary' => [
        1 => 'Da',
        0 => 'Nu',
        -1 => 'Nu știe/ Nu răspunde',
    ],

    'aggressor_relationship' => [
        'marital' => 'Maritală',
        'consensual' => 'Relație consensuală (Concubin)',
        'former_partner' => 'Fost partener',
        'parental' => 'Parentală sau asimilată (Agresorul e părinte)',
        'filial' => 'Filiație (Agresorul e Fiul/Fiica)',
        'other_related' => 'Altă relație de rudenie',
        'other' => 'Altă situație',
    ],

    'violence' => [
        'verbal' => 'Verbală',
        'psychological' => 'Psihologică',
        'physical' => 'Fizică',
        'sexual' => 'Sexuală',
        'economic' => 'Economică',
        'social' => 'Socială',
        'spiritual' => 'Spirituală',
        'cyber' => 'Cibernetică',
        'deprivation' => 'Prin deprivare/ neglijare',
    ],

    'drug' => [
        'alcohol_occasional' => 'Alcool ocazional',
        'alcohol_frequent' => 'Alcool frecvent',
        'tobacco' => 'Tutun',
        'tranquilizers' => 'Tranchilizante',
        'drugs' => 'Droguri',
        'other' => 'Altele',
    ],

    'aggressor_legal_history' => [
        'crimes' => 'Infracțiuni',
        'contraventions' => 'Contravenții',
        'protection_order' => 'Ordin de protecție',
    ],

    'presentation_mode' => [
        'spontaneous' => 'Spontan',
        'scheduled' => 'Programat',
        'forwarded' => 'Trimis de o instituție',
    ],

    'referral_mode' => [
        'verbal' => 'Verbal',
        'written' => 'Scris',
        'phone' => 'Telefonic',
        'brought' => 'Adus de reprezentant instituție',
    ],

    'notifier' => [
        'victim' => 'Victima',
        'aggressor' => 'Agresor',
        'child' => 'Copii',
        'other_related' => 'Alte rude',
        'neighbour' => 'Vecini',
        'other' => 'Alte persoane (specificați)',
    ],

    'notification_mode' => [
        'phone' => 'Telefonic',
        'personal' => 'Personal',
    ],

    'act_location' => [
        'domicile' => 'Domiciliul legal al victimei',
        'residence' => 'Reședința victimei',
        'public' => 'Loc public',
        'work' => 'Locul de muncă al victimei',
        'other' => 'Alt loc (specificați)',
    ],

    'organization_type' => [
        'ngo' => 'Organizație non-profit',
        'public' => 'Instituție publică',
        'private' => 'Entitate privată',
    ],

];
