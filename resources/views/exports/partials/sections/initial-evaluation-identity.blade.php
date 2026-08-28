@php($identity = $identity ?? [])
<div class="identity-wrapper">
    <table class="identity-form-table">
        <tr>
            <td class="identity-cell">
                <div class="identity-label">Nume:</div>
                <div class="identity-value">{{ $identity['last_name'] ?? '—' }}</div>
            </td>
            <td class="identity-cell">
                <div class="identity-label">Data nașterii:</div>
                <div class="identity-value">{{ $identity['birthdate'] ?? '—' }}</div>
            </td>
        </tr>
        <tr>
            <td class="identity-cell">
                <div class="identity-label">Prenume:</div>
                <div class="identity-value">{{ $identity['first_name'] ?? '—' }}</div>
            </td>
            <td class="identity-cell">
                <div class="identity-label">Locul nașterii:</div>
                <div class="identity-value">{{ $identity['birthplace'] ?? '—' }}</div>
            </td>
        </tr>
        <tr>
            <td class="identity-cell" colspan="2">
                <div class="identity-subsection-title">Domiciliu legal:</div>
                <div class="identity-checkbox-row">
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ ($identity['legal_environment'] ?? null) === 'rural' ? 'checked' : '' }}>
                        Rural
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ ($identity['legal_environment'] ?? null) === 'urban' ? 'checked' : '' }}>
                        Urban
                    </label>
                </div>
                <div class="identity-value identity-muted">
                    {{ $identity['legal_address'] ?? '—' }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="identity-cell" colspan="2">
                <div class="identity-subsection-title">Domiciliu efectiv:</div>
                <div class="identity-checkbox-row">
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ ($identity['effective_environment'] ?? null) === 'rural' ? 'checked' : '' }}>
                        Rural
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ ($identity['effective_environment'] ?? null) === 'urban' ? 'checked' : '' }}>
                        Urban
                    </label>
                </div>
                <div class="identity-value identity-muted">
                    {{ $identity['effective_address'] ?? '—' }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="identity-cell">
                <div class="identity-subsection-title">Act de identitate:</div>
                <div class="identity-value identity-muted">{{ $identity['id_type'] ?? '—' }}</div>
                <div class="identity-inline-row">
                    <div class="identity-inline-label">Serie:</div>
                    <div class="identity-inline-value">{{ $identity['id_serial'] ?? '—' }}</div>
                    <div class="identity-inline-label">nr.:</div>
                    <div class="identity-inline-value">{{ $identity['id_number'] ?? '—' }}</div>
                </div>
                <div class="identity-inline-row">
                    <div class="identity-inline-label">CNP:</div>
                    <div class="identity-inline-value">{{ $identity['cnp'] ?? '—' }}</div>
                </div>
            </td>
            <td class="identity-cell">
                <div class="identity-subsection-title">Date de contact:</div>
                <div class="identity-value">
                    Telefon personal: {{ $identity['primary_phone'] ?? '—' }}
                </div>
                <div class="identity-value">
                    Telefon de rezervă: {{ $identity['backup_phone'] ?? '—' }}
                </div>
                <div class="identity-value">
                    E-mail: {{ $identity['email'] ?? '—' }}
                </div>
                <div class="identity-value identity-muted">
                    Interval orar: {{ $identity['contact_notes'] ?? '—' }}
                </div>
            </td>
        </tr>
        <tr>
            <td class="identity-cell" colspan="2">
                <div class="identity-subsection-title">Cetățenia:</div>
                <div class="identity-checkbox-row">
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ ($identity['citizenship_is_romanian'] ?? false) ? 'checked' : '' }}>
                        Română
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ ($identity['citizenship_is_other'] ?? false) ? 'checked' : '' }}>
                        Altele
                    </label>
                </div>
                <div class="identity-subsection-spacer"></div>
                <div class="identity-subsection-title">Starea Civilă:</div>
                <div class="identity-checkbox-row">
                    @php($civilStatus = (string) ($identity['civil_status_value'] ?? ''))
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ $civilStatus === 'married' ? 'checked' : '' }}>
                        Căsătorit(ă)
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ $civilStatus === 'single' ? 'checked' : '' }}>
                        Necăsătorit(ă)
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ $civilStatus === 'divorced' ? 'checked' : '' }}>
                        Divorțat(ă)
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ $civilStatus === 'widowed' ? 'checked' : '' }}>
                        Văduv(ă)
                    </label>
                    <label class="identity-checkbox-label">
                        <input type="checkbox" disabled {{ $civilStatus === 'cohabitation' ? 'checked' : '' }}>
                        Uniunea liberă (concubinaj)
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td class="identity-cell" colspan="2">
                <div class="identity-subsection-title">Studii:</div>
                <div class="identity-checkbox-wrap">
                    @php($studiesValue = (string) ($identity['studies_value'] ?? ''))
                    @php($occValue = (string) ($identity['occupation_value'] ?? ''))
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'none' ? 'checked' : '' }}>
                        Fără studii
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'primary' ? 'checked' : '' }}>
                        Ciclu primar
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'secondary' ? 'checked' : '' }}>
                        Ciclu gimnazial
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'vocational' ? 'checked' : '' }}>
                        Școala profesională de arte și meserii
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'highschool' ? 'checked' : '' }}>
                        Liceu
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'postsecondary' ? 'checked' : '' }}>
                        Școală postliceală
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $studiesValue === 'highereducation' ? 'checked' : '' }}>
                        Studii superioare
                    </label>
                </div>
                <div class="identity-subsection-title">Ocupația:</div>
                <div class="identity-checkbox-wrap">
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'none' ? 'checked' : '' }}>
                        Fără ocupație
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'employee' ? 'checked' : '' }}>
                        Salariat
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'self_employed' ? 'checked' : '' }}>
                        Lucrător pe cont propriu
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'association_worker' ? 'checked' : '' }}>
                        Lucrător în asociație
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'business_owner' ? 'checked' : '' }}>
                        Patron
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'farmer' ? 'checked' : '' }}>
                        Agricultor
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'student' ? 'checked' : '' }}>
                        Elev/Student
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'unemployed' ? 'checked' : '' }}>
                        Șomer
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'domestic' ? 'checked' : '' }}>
                        Casnică
                    </label>
                    <label class="identity-checkbox-label identity-inline">
                        <input type="checkbox" disabled {{ $occValue === 'retired' ? 'checked' : '' }}>
                        Pensionar
                    </label>
                </div>
            </td>
        </tr>
    </table>
</div>
