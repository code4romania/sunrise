@php($d = $data ?? [])
@php($familyRows = $d['family_rows'] ?? [])
@php($supportGroupRows = $d['support_group_rows'] ?? [])
@php($childrenRows = $d['children_rows'] ?? [])
@php($interventionRows = $d['intervention_rows'] ?? [])
@php($resultRows = $d['result_rows'] ?? [])
@php($meetingsRows = $d['meetings_rows'] ?? [])

<div class="psych-sheet-title">FIȘĂ SOCIALĂ</div>

<table class="psych-sheet-meta-table">
    <tr><th>Instituția</th><td>{{ $d['institution'] ?? '—' }}</td><th>Numele furnizorului de servicii sociale</th><td>{{ $d['provider_name'] ?? '—' }}</td></tr>
    <tr><th>Data întocmirii fișei</th><td>{{ $d['sheet_date'] ?? '—' }}</td><th>Numele și prenumele asistentului social</th><td>{{ $d['specialist_name'] ?? '—' }}</td></tr>
    <tr><th>Număr înregistrare caz / Nr. dosar</th><td>{{ $d['case_number'] ?? '—' }}</td><th>Semnătura</th><td>{{ $d['signature'] ?? '........................................' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 1. Date despre victimă</div>
<table class="psych-sheet-meta-table">
    <tr><th>Nume și prenume</th><td>{{ $d['victim_name'] ?? '—' }}</td><th>Data nașterii</th><td>{{ $d['victim_birthdate'] ?? '—' }}</td></tr>
    <tr><th>Localitatea</th><td>{{ $d['victim_locality'] ?? '—' }}</td><th>Starea civilă</th><td>{{ $d['victim_civil_status'] ?? '—' }}</td></tr>
    <tr><th>Nume anterior</th><td>{{ $d['victim_prior_name'] ?? '—' }}</td><th>Relația actuală</th><td>{{ $d['victim_current_relationship'] ?? '—' }}</td></tr>
    <tr><th>Adresa de domiciliu</th><td colspan="3">{{ $d['victim_legal_address'] ?? '—' }}</td></tr>
    <tr><th>Adresa de reședință</th><td colspan="3">{{ $d['victim_effective_address'] ?? '—' }}</td></tr>
    <tr><th>Medic de familie</th><td>{{ $d['family_doctor'] ?? '—' }}</td><th>Detalii medic</th><td>{{ $d['family_doctor_details'] ?? '—' }}</td></tr>
    <tr><th>Starea de sănătate</th><td colspan="3">{{ $d['health_status'] ?? '—' }}</td></tr>
    <tr><th>Boli cronice</th><td>{{ $d['health_chronic'] ?? '—' }}</td><th>Boli degenerative</th><td>{{ $d['health_degenerative'] ?? '—' }}</td></tr>
    <tr><th>Boli psihice</th><td>{{ $d['health_psychic'] ?? '—' }}</td><th>Handicap / grad</th><td>{{ $d['disability'] ?? '—' }} / {{ $d['disability_degree'] ?? '—' }} ({{ $d['disability_type'] ?? '—' }})</td></tr>
    <tr><th>Asigurare de sănătate</th><td>{{ $d['health_insurance'] ?? '—' }}</td><th>Nivel de studii</th><td>{{ $d['studies'] ?? '—' }}</td></tr>
    <tr><th>Loc de muncă</th><td>{{ $d['workplace'] ?? '—' }}</td><th>Ocupația</th><td>{{ $d['occupation'] ?? '—' }}</td></tr>
    <tr><th>Experiența profesională</th><td colspan="3">{{ $d['professional_experience'] ?? '—' }}</td></tr>
    <tr><th>Date de contact victimă</th><td colspan="3">{{ $d['victim_contacts'] ?? '—' }}</td></tr>
    <tr><th>Persoană de contact de urgență</th><td>{{ $d['emergency_contact_exists'] ?? '—' }}</td><th>Nume / Telefon</th><td>{{ $d['emergency_contact_name'] ?? '—' }} / {{ $d['emergency_contact_phone'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 2. Date economice</div>
<table class="psych-sheet-meta-table">
    <tr><th>Venit net lunar victimă</th><td>{{ $d['net_income'] ?? '—' }}</td><th>Sursa venitului</th><td>{{ $d['income_source'] ?? '—' }}</td></tr>
    <tr><th>Beneficii sociale / observații</th><td colspan="3">{{ $d['social_benefits_notes'] ?? '—' }}</td></tr>
    <tr><th>Modalitate de plată</th><td colspan="3">{{ $d['payment_method'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 3. Relații sociale</div>
<table class="psych-sheet-data-table">
    <thead><tr><th>Relație</th><th>Nume și prenume</th><th>Vârsta</th><th>Localitatea</th><th>Ocupația</th><th>Relația</th><th>Susținere</th><th>Detalii susținere</th></tr></thead>
    <tbody>
    @forelse($familyRows as $row)
        <tr>
            <td>{{ $row['relationship'] ?? '—' }}</td>
            <td>{{ $row['name'] ?? '—' }}</td>
            <td>{{ $row['age'] ?? '—' }}</td>
            <td>{{ $row['locality'] ?? '—' }}</td>
            <td>{{ $row['occupation'] ?? '—' }}</td>
            <td>{{ $row['relation_note'] ?? '—' }}</td>
            <td>{{ $row['support'] ?? '—' }}</td>
            <td>{{ $row['support_observations'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

<table class="psych-sheet-data-table">
    <thead><tr><th>Persoane/Grup de suport</th><th>Nume</th><th>Susținere</th><th>Observații</th></tr></thead>
    <tbody>
    @forelse($supportGroupRows as $row)
        <tr>
            <td>{{ $row['relationship'] ?? '—' }}</td>
            <td>{{ $row['name'] ?? '—' }}</td>
            <td>{{ $row['support'] ?? '—' }}</td>
            <td>{{ $row['support_observations'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 4. Condiții de locuit</div>
<table class="psych-sheet-meta-table">
    <tr><th>Tipul de proprietate</th><td>{{ $d['home_type'] ?? '—' }}</td><th>Număr încăperi</th><td>{{ $d['rooms'] ?? '—' }}</td></tr>
    <tr><th>Număr persoane care locuiesc</th><td>{{ $d['peoples'] ?? '—' }}</td><th>Utilități</th><td>{{ $d['utilities'] ?? '—' }}</td></tr>
    <tr><th>Observații</th><td colspan="3">{{ $d['living_observations'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 5. Date despre copii</div>
<table class="psych-sheet-meta-table">
    <tr><th>Număr total de copii</th><td>{{ $d['children_total'] ?? '—' }}</td><th>Număr de copii în grija victimei</th><td>{{ $d['children_in_care'] ?? '—' }}</td></tr>
    <tr><th>Număr copii cu măsură de protecție</th><td>{{ $d['children_protection_measure'] ?? '—' }}</td><th>Număr copii în grija altei persoane</th><td>{{ $d['children_other_care'] ?? '—' }}</td></tr>
    <tr><th>Număr copii prezenți cu victima în serviciul social</th><td colspan="3">{{ $d['children_present_with_victim'] ?? '—' }}</td></tr>
</table>

<table class="psych-sheet-data-table social-children-table">
    <colgroup>
        <col style="width:3%;">
        <col style="width:9%;">
        <col style="width:4%;">
        <col style="width:8%;">
        <col style="width:5%;">
        <col style="width:10%;">
        <col style="width:7%;">
        <col style="width:11%;">
        <col style="width:7%;">
        <col style="width:3%;">
        <col style="width:3%;">
        <col style="width:6%;">
        <col style="width:6%;">
        <col style="width:6%;">
        <col style="width:6%;">
        <col style="width:6%;">
    </colgroup>
    <thead>
    <tr>
        <th>Nr.</th><th>Nume și prenume</th><th>Sex</th><th>Data nașterii</th><th>Vârsta</th><th>Localitatea</th><th>Ocupația</th><th>Loc de muncă/Unitate învățământ</th>
        <th>Stare sănătate</th><th>Medic familie</th><th>Alocație</th><th>Prezent cu victima</th><th>Acte identitate</th><th>Paternitate</th><th>Școlarizat</th><th>Caracterizarea relației</th>
    </tr>
    </thead>
    <tbody>
    @forelse($childrenRows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '—' }}</td>
            <td>{{ $row['name'] ?? '—' }}</td>
            <td>{{ $row['gender'] ?? '—' }}</td>
            <td>{{ $row['birthdate'] ?? '—' }}</td>
            <td>{{ $row['age'] ?? '—' }}</td>
            <td>{{ $row['locality'] ?? '—' }}</td>
            <td>{{ $row['occupation'] ?? '—' }}</td>
            <td>{{ $row['school_or_work'] ?? '—' }}</td>
            <td>{{ $row['health_status'] ?? '—' }}</td>
            <td>{{ $row['family_doctor'] ?? '—' }}</td>
            <td>{{ $row['allowance'] ?? '—' }}</td>
            <td>{{ $row['present_with_victim'] ?? '—' }}</td>
            <td>{{ $row['identity_docs'] ?? '—' }}</td>
            <td>{{ $row['paternity_recognized'] ?? '—' }}</td>
            <td>{{ $row['schooling'] ?? '—' }}</td>
            <td>{{ $row['relationship_characterization'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="16" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 6. Integrare și participare în serviciul social</div>
<table class="psych-sheet-meta-table">
    <tr><th>Comunicare</th><td>{{ $d['communication'] ?? '—' }}</td><th>Socializare</th><td>{{ $d['socialization'] ?? '—' }}</td></tr>
    <tr><th>Respectare regulament</th><td>{{ $d['rules_compliance'] ?? '—' }}</td><th>Participare la consiliere individuală</th><td>{{ $d['participation_in_individual_counseling'] ?? '—' }}</td></tr>
    <tr><th>Participare la activități comune</th><td>{{ $d['participation_in_joint_activities'] ?? '—' }}</td><th>Auto-gospodărire</th><td>{{ $d['self_management'] ?? '—' }}</td></tr>
    <tr><th>Comportament adictiv</th><td>{{ $d['addictive_behavior'] ?? '—' }}</td><th>Educație financiară</th><td>{{ $d['financial_education'] ?? '—' }}</td></tr>
    <tr><th>Observații</th><td colspan="3">{{ $d['integration_observations'] ?? '—' }}</td></tr>
</table>

<div class="psych-sheet-subtitle">Secțiunea 7. Intervenții</div>
<table class="psych-sheet-data-table">
    <thead><tr><th>Nr.</th><th>Intervenția</th><th>Obiective</th><th>Rezultate așteptate</th><th>Procedură</th><th>Indicatori</th><th>Grad de realizare</th></tr></thead>
    <tbody>
    @forelse($interventionRows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '—' }}</td>
            <td>{{ $row['name'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['objectives'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['expected_results'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['procedure'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['indicators'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['achievement_degree'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 8. Rezultate intervenții</div>
<table class="psych-sheet-data-table">
    <thead><tr><th>Nr.</th><th>Rezultat</th><th>Specialist</th><th>Inițiat la</th><th>Realizat/Soluționat la</th><th>Retras</th><th>Pierdut din monitorizare</th><th>Observații</th></tr></thead>
    <tbody>
    @forelse($resultRows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['result_name'] ?? '—' }}</td>
            <td>{{ $row['specialist'] ?? '—' }}</td>
            <td>{{ $row['started_at'] ?? '—' }}</td>
            <td>{{ $row['ended_at'] ?? '—' }}</td>
            <td>{{ $row['retried'] ?? '—' }}</td>
            <td>{{ $row['lost_from_monitoring'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['observations'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

<div class="psych-sheet-subtitle">Secțiunea 9. Monitorizare (intervenții din serviciu)</div>
<table class="psych-sheet-data-table">
    <thead><tr><th>Nr.</th><th>Data</th><th>Ora</th><th>Tip intervenție</th><th>Temă / Tip</th><th>Observații / Detalii</th><th>Durată (min)</th></tr></thead>
    <tbody>
    @forelse($meetingsRows as $row)
        <tr>
            <td>{{ $row['nr'] ?? '—' }}</td>
            <td>{{ $row['date'] ?? '—' }}</td>
            <td>{{ $row['time'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['intervention_name'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['topic'] ?? '—' }}</td>
            <td style="white-space: pre-wrap;">{{ $row['observations'] ?? '—' }}</td>
            <td>{{ $row['duration'] ?? '—' }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="monthly-sheet-empty">—</td></tr>
    @endforelse
    </tbody>
</table>

