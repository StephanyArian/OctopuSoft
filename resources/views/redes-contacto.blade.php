<x-app-layout>

    {{-- ESTILOS --}}
    <link rel="stylesheet" href="{{ asset('css/informacion-academica.css') }}">
    <link rel="stylesheet" href="{{ asset('css/redes-contacto.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>

    <div class="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shell">
                <div class="header-bar" style="display: none;"></div>
                <div class="body-row">
                    {{-- SIDEBAR --}}
                    @include('partials.sidebar-completar')
                    {{-- CONTENIDO PRINCIPAL --}}
                    <div class="main">

                        <div class="page-title">Redes profesionales y contacto</div>
                        <div class="title-underline"></div>

                        <div class="section-card">

                            <div class="section-subtitle">
                                Agrega tus enlaces profesionales y medios de contacto.
                                Usa el switch de cada campo para controlar su visibilidad en tu portafolio público.
                            </div>

                                 {{-- MENSAJES --}}
                            @if(session('success'))
                                <div class="alert-skill success">
                                    <div class="alert-skill-icon">✓</div>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert-skill error">
                                    <div class="alert-skill-icon">!</div>
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            
                            {{-- FORMULARIO --}}
                            <form method="POST" action="{{ route('redes.store') }}">
                                @csrf

                                {{-- LinkedIn --}}
                                <div class="form-group">
                                    <div class="form-group-header">
                                        <label for="input-linkedin">LinkedIn</label>

                                        <div class="switch-wrapper">
                                            <span class="switch-label-text {{ ($redes[$platforms['LinkedIn']]->is_visible ?? false) ? 'visible' : 'hidden' }}" id="lbl-linkedin">
                                                {{ ($redes[$platforms['LinkedIn']]->is_visible ?? false) ? 'Visible' : 'Oculto' }}
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" name="visible_linkedin" value="1"
                                                       class="privacy-switch" data-field="linkedin"
                                                       {{ ($redes[$platforms['LinkedIn']]->is_visible ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="url" id="input-linkedin" name="linkedin" class="form-input"
                                           placeholder="Ej. https://linkedin.com/in/usuario"
                                           value="{{ old('linkedin', $redes[$platforms['LinkedIn']]->profile_url ?? '') }}">
                                    <div class="privacy-hint" id="hint-linkedin">
                                        
                                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1a7 7 0 100 14A7 7 0 008 1z" stroke="#999" stroke-width="1.2"/><path d="M8 7v5M8 5v.5" stroke="#999" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        Este campo es visible en tu portafolio público
                                    </div>
                                    
                                    @if(session('warning_linkedin'))
                                        <p class="field-warning">{{ session('warning_linkedin') }}</p>
                                    @endif
                                    
                                </div>

                                {{-- GitHub --}}
                                <div class="form-group">
                                    <div class="form-group-header">
                                        <label for="input-github">GitHub</label>
                                        <div class="switch-wrapper">
                                            <span class="switch-label-text {{ ($redes[$platforms['GitHub']]->is_visible ?? false) ? 'visible' : 'hidden' }}" id="lbl-github">
                                                {{ ($redes[$platforms['GitHub']]->is_visible ?? false) ? 'Visible' : 'Oculto' }}
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" name="visible_github" value="1"
                                                       class="privacy-switch" data-field="github"
                                                       {{ ($redes[$platforms['GitHub']]->is_visible ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="url" id="input-github" name="github" class="form-input"
                                           placeholder="Ej. https://github.com/usuario"
                                           value="{{ old('github', $redes[$platforms['GitHub']]->profile_url ?? '') }}">
                                    <div class="privacy-hint" id="hint-github">
                                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1a7 7 0 100 14A7 7 0 008 1z" stroke="#999" stroke-width="1.2"/><path d="M8 7v5M8 5v.5" stroke="#999" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        Este campo es visible en tu portafolio público
                                    </div>
                                    @if(session('warning_github'))
                                        <p class="field-warning"> {{ session('warning_github') }}</p>
                                    @endif
                                    
                                </div>

                                {{-- WhatsApp --}}
                                <div class="form-group">
                                    <div class="form-group-header">
                                        <label for="input-whatsapp">WhatsApp</label>
                                        <div class="switch-wrapper">
                                            <span class="switch-label-text {{ ($redes[$platforms['WhatsApp']]->is_visible ?? false) ? 'visible' : 'hidden' }}" id="lbl-whatsapp">
                                                {{ ($redes[$platforms['WhatsApp']]->is_visible ?? false) ? 'Visible' : 'Oculto' }}
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" name="visible_whatsapp" value="1"
                                                       class="privacy-switch" data-field="whatsapp"
                                                       {{ ($redes[$platforms['WhatsApp']]->is_visible ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="text" id="input-whatsapp" name="whatsapp" class="form-input"
                                           placeholder="Ej. 61234556" pattern="^\+?[0-9]{8,15}$"
                                           value="{{ old('whatsapp', isset($redes[$platforms['WhatsApp']]) ? preg_replace('/https:\/\/wa\.me\//', '', $redes[$platforms['WhatsApp']]->profile_url) : '') }}">
                                    <div class="privacy-hint" id="hint-whatsapp">
                                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1a7 7 0 100 14A7 7 0 008 1z" stroke="#999" stroke-width="1.2"/><path d="M8 7v5M8 5v.5" stroke="#999" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        Este campo es visible en tu portafolio público
                                    </div>
                                    @if(session('warning_whatsapp'))
                                        <p class="field-warning"> {{ session('warning_whatsapp') }}</p>
                                    @endif
                                    
                                </div>

                                {{-- Correo --}}
                                <div class="form-group">
                                    <div class="form-group-header">
                                        <label for="input-email_contacto">Correo</label>
                                        <div class="switch-wrapper">
                                            <span class="switch-label-text {{ ($redes[$platforms['Email']]->is_visible ?? false) ? 'visible' : 'hidden' }}" id="lbl-email_contacto">
                                                {{ ($redes[$platforms['Email']]->is_visible ?? false) ? 'Visible' : 'Oculto' }}
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" name="visible_email" value="1"
                                                       class="privacy-switch" data-field="email_contacto"
                                                       {{ ($redes[$platforms['Email']]->is_visible ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="email" id="input-email_contacto" name="email_contacto" class="form-input"
                                           pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Solo correos Gmail"
                                           value="{{ old('email_contacto', $redes[$platforms['Email']]->profile_url ?? '') }}">
                                    <div class="privacy-hint" id="hint-email_contacto">
                                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1a7 7 0 100 14A7 7 0 008 1z" stroke="#999" stroke-width="1.2"/><path d="M8 7v5M8 5v.5" stroke="#999" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        Este campo está oculto — solo tú puedes verlo
                                    </div>

                                    @if(session('warning_email_contacto'))
                                        <p class="field-warning">{{ session('warning_email_contacto') }}</p>
                                    @endif
                                    
                                </div>

                                {{-- Otros --}}
                                <div class="form-group">
                                    <div class="form-group-header">
                                        <label for="input-otros">Otros</label>
                                        <div class="switch-wrapper">
                                            <span class="switch-label-text {{ ($redes[$platforms['Otros']]->is_visible ?? false) ? 'visible' : 'hidden' }}" id="lbl-otros">
                                                {{ ($redes[$platforms['Otros']]->is_visible ?? false) ? 'Visible' : 'Oculto' }}
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" name="visible_otros" value="1"
                                                       class="privacy-switch" data-field="otros"
                                                       {{ ($redes[$platforms['Otros']]->is_visible ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="text" id="input-otros" name="otros" class="form-input"
                                           maxlength="50"
                                           value="{{ old('otros', $redes[$platforms['Otros']]->profile_url ?? '') }}">
                                    <div class="privacy-hint" id="hint-otros">
                                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1a7 7 0 100 14A7 7 0 008 1z" stroke="#999" stroke-width="1.2"/><path d="M8 7v5M8 5v.5" stroke="#999" stroke-width="1.2" stroke-linecap="round"/></svg>
                                        Este campo está oculto — solo tú puedes verlo
                                    </div>


                                </div>

                                {{-- UBICACIÓN FÍSICA --}}
                                <div class="section-separator">Ubicación física</div>

                                <div class="form-group">
                                    <div class="form-group-header">
                                        <label for="addr-input">Dirección</label>
                                        <div class="switch-wrapper">
                                            <span class="switch-label-text {{ ($location->show_location ?? false) ? 'visible' : 'hidden' }}" id="lbl-addr">
                                                {{ ($location->show_location ?? false) ? 'Visible' : 'Oculta' }}
                                            </span>
                                            <label class="switch">
                                                <input type="checkbox" id="sw-addr" name="show_location"
                                                       class="privacy-switch" data-field="addr" value="1"
                                                       {{ ($location->show_location ?? false) ? 'checked' : '' }}>
                                                <span class="switch-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Input con autocompletado --}}
                                    <div style="display:flex; gap:8px;">
                                        <div style="flex:1; position:relative;">
                                            <input type="text" id="addr-input" name="address" class="form-input"
                                                   placeholder="Escribe tu dirección..." autocomplete="off"
                                                   value="{{ old('address', $location->address ?? '') }}">
                                            <div id="search-dropdown"></div>
                                        </div>
                                        <button type="button" id="btn-buscar-addr" class="btn primary"
                                                style="white-space:nowrap; padding:10px 16px;">
                                            Buscar
                                        </button>
                                    </div>

                                    {{-- Coordenadas ocultas --}}
                                    <input type="hidden" id="input-lat" name="latitude"  value="{{ $location->latitude  ?? '' }}">
                                    <input type="hidden" id="input-lng" name="longitude" value="{{ $location->longitude ?? '' }}">
                                   {{-- Nombre completo de la dirección para guardar correctamente --}}
                                    <input type="hidden" id="input-address-raw" name="address_raw"
                                           value="{{ old('address_raw', $location->address ?? '') }}">
                                    <div class="privacy-hint {{ ($location->show_location ?? false) ? 'hint-visible' : '' }}" id="hint-addr">
                                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 1a7 7 0 100 14A7 7 0 008 1z" stroke="currentColor" stroke-width="1.2"/>
                                            <path d="M8 7v5M8 5v.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                        </svg>
                                        {{ ($location->show_location ?? false) ? 'Tu ubicación es visible en tu portafolio público' : 'Tu dirección está oculta — solo tú puedes verla' }}
                                    </div>
                                </div>

                                {{-- Botón ver mapa completo --}}
                                <div class="map-toolbar">
                                    <button type="button" id="btn-expand-map" class="btn-expand">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/>
                                        </svg>
                                        Ver mapa completo
                                    </button>
                                </div>

                                {{-- Mapa pequeño --}}
                                <div class="map-container">
                                    <div class="map-placeholder" id="map-placeholder"
                                         style="{{ ($location->show_location ?? false) ? 'display:none' : '' }}">
                                        <svg class="map-placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                            <circle cx="12" cy="9" r="2.5"/>
                                        </svg>
                                        <div class="map-placeholder-title">Dirección no disponible</div>
                                        <div class="map-placeholder-sub">Activa la visibilidad para mostrar tu ubicación en el mapa</div>
                                    </div>
                                    <div id="leaflet-map"
                                         data-lat="{{ $location->latitude ?? -17.3895 }}"
                                         data-lng="{{ $location->longitude ?? -66.1568 }}"
                                         style="{{ ($location->show_location ?? false) ? 'display:block' : 'display:none' }}">
                                    </div>
                                </div>

                                {{-- BOTÓN GUARDAR --}}
                                <div class="btn-row">
                                    <button type="submit" class="btn primary">Guardar cambios</button>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL MAPA GRANDE — fuera del formulario pero dentro del layout --}}
    <div id="map-modal">
        <div class="map-modal-inner">
            <div class="map-modal-header">
                <div>
                    <div class="map-modal-title">Selecciona tu ubicación</div>
                    <div class="map-modal-hint">Haz click en el mapa o arrastra el pin para ajustar tu dirección</div>
                </div>
                <button type="button" id="btn-close-modal" class="btn-close-modal">✕</button>
            </div>
            <div id="leaflet-map-modal"></div>
        </div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    {{-- JS de la vista --}}
    <script src="{{ asset('js/redes-contacto.js') }}"></script>

</x-app-layout>