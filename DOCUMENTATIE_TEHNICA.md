# Sunrise - Documentatie tehnica completa

## 1. Scopul aplicatiei

Sunrise este o aplicatie Laravel pentru managementul cazurilor sociale in contextul violentei domestice, folosita de institutii si ONG-uri. Platforma acopera fluxul complet al unui caz: inregistrare, evaluare, interventie, monitorizare, inchidere, raportare si export.

## 2. Stack tehnologic si arhitectura

- Backend: Laravel 12, PHP 8.4
- UI administrativ/operational: Filament v4 + Livewire v3
- Frontend styling: Tailwind CSS v4
- Autentificare API: Sanctum
- Cautare: Laravel Scout (database + configurare Typesense)
- Media/documente: Spatie Media Library
- Exporturi: maatwebsite/excel, barryvdh/laravel-dompdf
- Monitorizare erori: Sentry

Aplicatia foloseste doua panel-uri Filament:

- Panel `admin` la ruta `/admin` pentru administrarea sistemului
- Panel `organization` la ruta `/` pentru operare pe tenant (organizatie)

Multi-tenancy este configurat pe modelul `Organization`, identificat prin `slug`.

## 3. Structura functionala pe module

### 3.1 Modul Admin

Module principale disponibile in panel-ul de admin:

- Institutii
- Organizatii (in relatie cu institutii)
- Utilizatori
- Roluri
- Servicii
- Beneficii
- Rezultate
- Nomenclatoare

Responsabilitati:

- configurare ecosistem institutional
- administrare utilizatori si roluri
- management nomenclatoare globale

### 3.2 Modul Organization (operational)

Module principale disponibile in panel-ul organization:

- Cazuri (`CaseResource`)
- Personal / echipa (`StaffResource`)
- Servicii (`ServiceResource`)
- Rapoarte (`ReportsNewPage`)
- Profil organizatie (tenant profile)

Acest panel este orientat pe activitatea zilnica a echipei care gestioneaza cazuri.

## 4. Model de date (entitati principale)

### 4.1 Entitati de organizare si acces

- `Institution`
- `Organization`
- `User`
- `Role`
- `UserRole`
- `OrganizationUserPermissions`
- `UserStatus`

### 4.2 Entitati nucleu caz

- `Beneficiary` (entitatea centrala de caz)
- `BeneficiaryDetails`
- `BeneficiaryAntecedents`
- `BeneficiaryPartner`
- `Children`
- `Aggressor`
- `FlowPresentation`
- `Document`
- `CloseFile`

### 4.3 Entitati evaluare

- `EvaluateDetails`
- `Violence`
- `RiskFactors`
- `RequestedServices`
- `BeneficiarySituation`
- `MultidisciplinaryEvaluation`
- `DetailedEvaluationResult`
- `DetailedEvaluationSpecialist`
- `ViolenceHistory`
- `Meeting`

### 4.4 Entitati interventie si planificare

- `InterventionPlan`
- `InterventionService`
- `BeneficiaryIntervention`
- `InterventionMeeting`
- `MonthlyPlan`
- `MonthlyPlanService`
- `MonthlyPlanInterventions`
- `InterventionPlanResult`
- `ServiceCounselingSheet`

### 4.5 Entitati suport si nomenclatoare

- `Service`
- `ServiceIntervention`
- `OrganizationService`
- `OrganizationServiceIntervention`
- `Benefit`
- `BenefitType`
- `BenefitService`
- `Result`
- `ReferringInstitution`
- `Country`
- `County`
- `City`
- `Address`

## 5. Fluxuri de business principale

### 5.1 Lifecycle caz

1. Creare caz in wizard (consimtamant, CNP, identitate, copii, informatii de caz, echipa)
2. Evaluare initiala (detalii evaluare, violenta, factori de risc, servicii solicitate, situatie beneficiar)
3. Evaluare detaliata (specialisti, rezultate, completari specifice)
4. Plan interventie (servicii, interventii, intalniri, planificare lunara)
5. Monitorizare (fise si urmarire evolutie)
6. Inchidere dosar (admitere/iesire, motiv, validari finale)

### 5.2 CNP lookup si deduplicare operationala

La creare caz, daca exista CNP, sistemul poate cauta in tenant/alte centre relevante si poate prefila date, reducand dublurile si timpul de introducere.

### 5.3 Cazuri relationate

`Beneficiary` foloseste referinte precum `initial_id` pentru lanturi de cazuri (reactivari sau cazuri legate).

## 6. Rute si puncte de intrare

- Web:
  - `/laravel/login` (redirect catre login admin Filament)
- API:
  - `/api/user` (protejat cu `auth:sanctum`)
- Filament:
  - `/admin` pentru administrare
  - `/` pentru panel organization
  - `/welcome/{user:ulid}` in ambele panel-uri
- Health check:
  - `/up`

## 7. Securitate si autorizare

- Guard principal: `web`
- API auth: Sanctum
- Control acces panel:
  - `User::canAccessPanel()` pentru separarea admin vs organization
  - `canAccessTenant()` pentru validare apartenenta la organizatie
- Middleware custom:
  - `EnsureUserIsActive`
  - `UpdateDefaultTenant`
- Policies pe entitati cheie:
  - `BeneficiaryPolicy`
  - `UserPolicy`
  - `DocumentPolicy`
  - `MonitoringPolicy`
- Permisiuni business prin roluri + `OrganizationUserPermissions`
- Rate limiting API configurat la 60 request-uri/minut (per user/IP)

## 8. Automatizari, comenzi si evenimente

### 8.1 Comenzi custom Artisan

- `scout:rebuild`  
  Reface indexurile de cautare pentru modelele configurate in Scout.

- `beneficiaries:send-monitoring-sheet-reminders`  
  Trimite remindere pentru fisele de monitorizare ale cazurilor eligibile.

### 8.2 Scheduling

Aplicatia programeaza rularea zilnica (ora 08:00) a reminderelor de monitorizare.

### 8.3 Listeners

- `LogSuccessfulLogin`  
  Inregistreaza activitatea de autentificare (inclusiv metadate de context).

### 8.4 Notificari

- Welcome notification (admin)
- Welcome notification (organization)
- Password reset
- Reminder monitorizare

## 9. Rapoarte si exporturi

### 9.1 Rapoarte operationale

Paginile de raportare permit selectii de criterii si generare de rapoarte pe cazuri/beneficiari in functie de indicatori (de exemplu: tipuri violenta, risc, venit, gen, varsta, distributii).

### 9.2 Exporturi

`CaseExportManager` centralizeaza exporturile specifice de caz (PDF/CSV/XLS), incluzand documente de identitate, informatii de caz, monitorizare, inchidere dosar, planuri lunare si alte componente operationale.

## 10. Integrari externe si infrastructura

- Sentry pentru error tracking
- Mail providers: SMTP, Mailgun, Postmark, SES
- Scout cu suport database/Typesense (si configurari pentru alti provideri)
- Spatie Media Library pentru atasamente si organizare fisiere
- DomPDF pentru PDF
- Laravel Excel pentru XLS
- Guzzle HTTP client pentru apeluri externe

## 11. Stocare fisiere si media

Sunt configurate disk-urile:

- `local`
- `public`
- `private`
- `s3`

Media Library foloseste reguli de path generation pentru separarea fisierelor pe tenant/utilizator.

## 12. Legacy si compatibilitate

Codul include un namespace legacy `App\OldFilament` (resurse/pagini vechi). Panel-urile active descopera resursele din `app/Filament/...`, iar zona legacy este pastrata in repository pentru compatibilitate istorica si referinta.

## 13. Testare si acoperire

Proiectul foloseste Pest. Structura testelor:

- `tests/Feature` pentru fluxuri end-to-end (admin/organization)
- `tests/Unit` pentru servicii, utilitare, generatoare de rapoarte

Zone acoperite:

- CRUD si validari (ex. institutii)
- creare/editare caz
- exporturi de caz
- raportare
- servicii de business (ex. lookup CNP)

## 14. Configurari relevante

Fisiere de configurare importante:

- `config/auth.php`
- `config/sanctum.php`
- `config/filament.php`
- `config/scout.php`
- `config/queue.php`
- `config/filesystems.php`
- `config/media-library.php`
- `config/services.php`
- `config/mail.php`
- `config/sentry.php`

## 15. Fisiere cheie pentru mentenanta

- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/OrganizationPanelProvider.php`
- `app/Filament/Organizations/Resources/Cases/CaseResource.php`
- `app/Services/CaseExports/CaseExportManager.php`
- `app/Services/Reports/`
- `app/Console/Commands/RebuildScoutCommand.php`
- `app/Console/Commands/SendMonitoringSheetRemindersCommand.php`
- `routes/web.php`
- `routes/api.php`
- `bootstrap/app.php`

## 16. Observatii operationale

- Aplicatia este orientata pe fluxuri institutionale multi-tenant.
- Cea mai mare complexitate este in managementul ciclului de viata al cazului si in zona de raportare/export.
- Pentru evolutie sigura, se recomanda mentinerea testelor pe fluxurile critice de caz, export si autorizare.
