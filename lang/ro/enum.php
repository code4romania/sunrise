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

    'user_status' => [
        'active' => 'Activ',
        'inactive' => 'Dezactivat',
        'pending' => 'În așteptare',
    ],

    'case_permissions' => [
        'has_access_to_all_cases' => 'Are acces la toate cazurile din Centru',
        'can_search_cases_in_all_centers' => 'Poate căuta cazuri (după CNP) în baza de date a tuturor centrelor instituției',
        'can_copy_cases_in_all_centers' => 'Poate copia date identificare beneficiar dintr-o bază de date în alta  a instituției',
        'has_access_to_statistics' => 'Are acces la rapoarte statistice',
    ],

    'admin_permission' => [
        'can_change_nomenclature' => 'Are drepturi de modificare nomenclator',
        'can_change_staff' => 'Are drepturi de modificare Echipă Specialiști (Staff)',
        'can_change_organisation_profile' => 'Are drepturi de modificare Profilul Organizației în Rețeaua Sunrise',
    ],

    'frequency' => [
        'daily' => 'Zilnică',
        'weekly' => 'Săptămânală',
        'monthly' => 'Lunară',
        'lass_than_monthly' => 'Mai rar decât lunară',
    ],

    'helps' => [
        'temporary_shelter' => 'Găzduire temporară',
        'emergency_bag_storage' => 'Păstrare bagaj urgență',
        'financial_support' => 'Sprijin financiar',
        'emotional_support' => 'Sprijin emoțional',
        'accompanying_actions' => 'Acompaniere demersuri',
        'emergency_call' => 'Apelare de urgență',
    ],

    'applicant' => [
        'beneficiary' => 'Solicitare din partea beneficiarului',
        'other' => 'Semnalare caz de către altcineva',
    ],

    'level' => [
        'high' => 'Grad de risc crescut',
        'medium' => 'Grad de risc mediu',
        'low' => 'Grad de risc scăzut',
        'none' => 'Fără risc documentat',
    ],

    'role' => [
        'coordinator' => 'Coordonator',
        'manger' => 'Manager de caz',
        'chef_manager' => 'Șef manager de caz',
        'chef_service' => 'Șef serviciu',
        'psychological_advice' => 'Consilier Psihologic',
        'psychotherapist' => 'Psihoterapeut',
        'clinical_psychologist' => 'Psiholog Clinician',
        'psycho_pedagogue' => 'Psihopedagog',
        'social_worker' => 'Asistent social',
        'legal_advisor' => 'Consilier juridic',
        'facilitator' => 'Facilitator',
        'trainer' => 'Formator',
        'doctor' => 'Medic',
        'medical_assistant' => 'Asistent medical',
        'occupational_therapist' => 'Terapeut ocupațional',
        'other' => 'Alt Specialist',
    ],
    'ethnicity' => [
        'romanian' => 'Română',
        'hungarian' => 'Maghiară',
        'roma' => 'Romă',
        'ukrainian' => 'Ucrainienă',
        'german' => 'Germană',
        'russian_lippovan' => 'Ruso-lipoveană',
        'turkish' => 'Turcă',
        'tatar' => 'Tătară',
        'serbian' => 'Sârbă',
        'other' => 'Alta',
    ],

    'citizenship' => [
        'romanian' => 'Română',
        'moldavian' => 'Moldovenească',
        'italian' => 'Italiană',
        'german' => 'Germană',
        'ukrainian' => 'Ucraineană',
        'hungarian' => 'Maghiară',
        'turkish' => 'Turcă',
        'syrian' => 'Siriană',
        'chinese' => 'Chineză',
        'french' => 'Franceză',
        'bulgarian' => 'Bulgară',
        'israeli' => 'Israeliană',
        'serbian' => 'Sârbă',
        'greek' => 'Greacă',
        'russian' => 'Rusă',
        'lebanese' => 'Libaneză',
        'other' => 'Alta',
    ],
    'document_type' => [
        'contract' => 'Contract',
        'form' => 'Forumular',
        'file' => 'Fișă',
        'request' => 'Cerere',
        'accord' => 'Acord',
        'decision' => 'Decizie',
        'certificate' => 'Adeverință',
        'declaration' => 'Declarație',
        'notification' => 'Sesizare',
        'report' => 'Raport',
        'verbal_process' => 'Proces verbal',
        'id_card' => 'Act identitate beneficiar',
        'id_card_child' => 'Act identitate copil',
        'civil_status' => 'Act stare civilă',
        'studies_document' => 'Act studii',
        'propriety_document' => 'Act proprietate',
        'medical_document' => 'Document medical',
        'medico_legal_certificate' => 'Certificat medico-legal',
        'protection_order' => 'Ordin de protecție',
        'legal_document' => 'Document legal',
        'evaluation_questionnaire' => 'Chestionar evaluare',
        'child_born_certificate' => 'Certificat de naștere copil',
        'standard_sheets' => 'Fișe standard',
        'document' => 'Document (nespecificat)',
    ],

    'report_type' => [
        'cases_by_age' => 'Vârsta victimei (grupe de vârstă)',
        'cases_by_age_segmentation' => 'Vârsta victimei (statut de minor/major)',
        'cases_by_gender' => 'Genul victimei',
        'cases_by_citizenship' => 'Cetățenia victimei',
        'cases_by_ethnicity' => 'Etnia victimei',
        'cases_by_civil_status' => 'Starea civilă a victimei',
        'cases_by_civil_status_and_gender' => 'Starea civilă și genul victimei',
        'cases_by_civil_status_and_age' => 'Starea civilă și grupa de vârstă a victimei',
        'cases_by_studies' => 'Nivelul de studii al victimei',
        'cases_by_studies_and_gender' => 'Nivelul de studii și genul victimei',
        'cases_by_studies_and_effective_address' => 'Nivelul de studii și domiciliul efectiv al victimei',
        'cases_by_studies_and_age' => 'Nivelul de studii și vârsta victimei (minor/major)',
        'cases_by_legal_address' => 'Domiciliul legal al victimei',
        'cases_by_effective_address' => 'Domiciliul efectiv al victimei',
        'cases_by_occupation' => 'Ocupația victimei',
        'cases_by_occupation_and_effective_address' => 'Ocupația și domiciliul efectiv al victimei',
        'cases_by_occupation_and_effective_address_and_gender' => 'Ocupația, domiciliul efectiv și genul victimei',
        'cases_by_age_gender_and_legal_address' => 'Grupa de vârstă, genul și domiciliul legal al victimei',
        'cases_by_age_gender_and_effective_address' => 'Grupa de vârstă, genul și domiciliul efectiv al victimei',
        'cases_by_home_ownership' => 'Dreptul de proprietate asupra locuinței primare',
        'cases_by_home_ownership_and_effective_address' => 'Dreptul de proprietate asupra locuinței primare și domiciliul efectiv',
        'cases_by_home_ownership_effective_address_and_gender' => 'Dreptul de proprietate asupra locuinței primare, domiciliul efectiv și genul victimei',
        'cases_by_income' => 'Încadrarea în venit a victimei',
        'cases_by_income_and_effective_address' => 'Încadrarea în venit și domiciliul efectiv al victimei',
        'cases_by_income_effective_address_and_gender' => 'Încadrarea în venit, domiciliul efectiv și genul victimei',
        'cases_by_aggressor_relationship' => 'Relația cu agresorul',
        'cases_by_aggressor_relationship_and_age' => 'Relația cu agresorul și grupa de vârstă a victimei',
        'cases_by_aggressor_relationship_and_age_and_gender' => 'Relația cu agresorul, genul victimei și grupa de vârstă a victimei',
        'cases_by_primary_violence_type' => 'Tipul de violență primară',
        'cases_by_violence_types' => 'Tipul de violență (selecție multiplă a tuturor tipurilor de violență)',
        'cases_by_violence_frequency' => 'Frecvența agresiunii',
        'cases_by_primary_violence_type_and_age' => 'Tipul de violență primară și vârsta victimei (minor/major)',
        'cases_by_primary_violence_frequency_and_age' => 'Tipul de violență primară, frecvența agresiunii și vârsta victimei (minor/major)',
        'cases_by_presentation_mode' => 'Modalitatea de prezentare a victimei',
        'cases_by_referring_institution' => 'Instituția care trimite victima',
    ],

    'age_interval' => [
        'under_1_year' => '< 1',
        'between_1_and_2_years' => '1-2',
        'between_3_and_6_years' => '3-6',
        'between_7_and_9_years' => '7-9',
        'between_10_and_13_years' => '10-13',
        'between_14_and_17_years' => '14-17',
        'between_18_and_25_years' => '18-25',
        'between_26_and_35_years' => '26-35',
        'between_36_and_45_years' => '36-45',
        'between_46_and_55_years' => '46-55',
        'between_56_and_65_years' => '56-65',
        'over_65_years' => '65 +',
    ],

    'beneficiary_segmentation_by_age' => [
        'minor' => '0-17 ani (minor)',
        'major' => '18 ani + (major)',
    ],
];
