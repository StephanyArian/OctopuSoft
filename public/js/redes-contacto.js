(function () {
    'use strict';

    var map = null;
    var marker = null;
    var mapModal = null;
    var markerModal = null;
    var searchTimeout = null;
    var dropdownMousedown = false;

    document.addEventListener('DOMContentLoaded', function () {
        initSwitches();
        initAddressSearch();
        initModalMap();
        initMapIfVisible();
    });

    // -----------------------------------------------
    // 1. SWITCHES
    // -----------------------------------------------
    function initSwitches() {
        document.querySelectorAll('.privacy-switch').forEach(function (sw) {
            updateFieldState(sw);
            sw.addEventListener('change', function () {
                updateFieldState(sw);
                if (sw.id === 'sw-addr') toggleMap(sw.checked);
            });
        });
    }

    function updateFieldState(sw) {
        var f      = sw.dataset.field;
        var lbl    = document.getElementById('lbl-'   + f);
        var inp    = document.getElementById('input-' + f);
        var hint   = document.getElementById('hint-'  + f);
        if (!lbl) return;

        if (sw.checked) {
            lbl.textContent = 'Visible';
            lbl.className   = 'switch-label-text visible';
            if (inp)  inp.classList.remove('field-hidden');
            if (hint) {
                hint.className   = 'privacy-hint hint-visible';
                hint.textContent = (f === 'addr')
                    ? 'Tu ubicación es visible en tu portafolio público'
                    : 'Este campo es visible en tu portafolio público';
            }
        } else {
            lbl.textContent = (f === 'addr') ? 'Oculta' : 'Oculto';
            lbl.className   = 'switch-label-text hidden';
            if (inp)  inp.classList.add('field-hidden');
            if (hint) {
                hint.className   = 'privacy-hint';
                hint.textContent = (f === 'addr')
                    ? 'Tu dirección está oculta — solo tú puedes verla'
                    : 'Este campo está oculto — solo tú puedes verlo';
            }
        }
    }

    // -----------------------------------------------
    // 2. TOGGLE MAPA
    // FIX: solo destruye el mapa si se está ocultando,
    //      nunca al mostrarlo (evita condición de carrera)
    // -----------------------------------------------
    function toggleMap(visible) {
        var placeholder = document.getElementById('map-placeholder');
        var mapDiv      = document.getElementById('leaflet-map');
        if (!placeholder || !mapDiv) return;

        if (visible) {
            placeholder.style.display = 'none';
            mapDiv.style.display      = 'block';
            // FIX: NO destruir el mapa aquí — buildMap() reutiliza si ya existe
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { buildMap(); });
            });
        } else {
            placeholder.style.display = 'flex';
            mapDiv.style.display      = 'none';
            // Solo destruir al ocultar, para liberar recursos
            if (map) { map.remove(); map = null; marker = null; }
        }
    }

    // -----------------------------------------------
    // 3. MAPA PRINCIPAL
    // -----------------------------------------------
    function buildMap() {
        var mapDiv = document.getElementById('leaflet-map');
        if (!mapDiv || typeof L === 'undefined') return;

        var rect = mapDiv.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            setTimeout(buildMap, 200);
            return;
        }

        // FIX: leer siempre de los inputs primero (ya actualizados por goToResult),
        //      luego data-lat/lng (del servidor), luego coordenadas por defecto
        var latEl = document.getElementById('input-lat');
        var lngEl = document.getElementById('input-lng');
        var lat = (latEl && latEl.value !== '') ? parseFloat(latEl.value) : null;
        var lng = (lngEl && lngEl.value !== '') ? parseFloat(lngEl.value) : null;

        if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
            lat = parseFloat(mapDiv.dataset.lat) || -17.3895;
            lng = parseFloat(mapDiv.dataset.lng) || -66.1568;
        }

        if (map) {
            // Mapa ya existe: solo reposicionar
            map.invalidateSize();
            map.setView([lat, lng], map.getZoom() || 15);
            if (marker) marker.setLatLng([lat, lng]);
            return;
        }

        // Crear mapa nuevo
        map = L.map('leaflet-map', { scrollWheelZoom: true, zoomControl: true })
               .setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        marker = L.marker([lat, lng], { icon: makeIcon(), draggable: true }).addTo(map);

        marker.on('dragend', function (e) {
            var pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng, function (addr) {
                if (addr) setAddress(addr);
                if (markerModal) markerModal.setLatLng([pos.lat, pos.lng]);
            });
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng, function (addr) {
                if (addr) setAddress(addr);
                if (markerModal) markerModal.setLatLng([e.latlng.lat, e.latlng.lng]);
            });
        });
    }

    // ---- Pin estilo Google Maps ----
    function makeIcon() {
        return L.divIcon({
            html: '<div style="' +
                'width:28px;height:28px;' +
                'background:#1abc9c;' +
                'border-radius:50% 50% 50% 0;' +
                'transform:rotate(-45deg);' +
                'border:3px solid #fff;' +
                'box-shadow:0 2px 8px rgba(0,0,0,0.35);' +
                'cursor:grab;' +
            '"><div style="' +
                'width:8px;height:8px;' +
                'background:#fff;' +
                'border-radius:50%;' +
                'position:absolute;' +
                'top:50%;left:50%;' +
                'transform:translate(-50%,-50%)' +
            '"></div></div>',
            className: '',
            iconSize:   [28, 28],
            iconAnchor: [14, 28],
            popupAnchor:[0, -30]
        });
    }

    function updateCoords(lat, lng) {
        var latEl = document.getElementById('input-lat');
        var lngEl = document.getElementById('input-lng');
        if (latEl) latEl.value = lat.toFixed(7);
        if (lngEl) lngEl.value = lng.toFixed(7);
        // FIX: también actualizar data-lat/lng para que buildMap() use coords correctas
        var mapDiv = document.getElementById('leaflet-map');
        if (mapDiv) {
            mapDiv.dataset.lat = lat.toFixed(7);
            mapDiv.dataset.lng = lng.toFixed(7);
        }
    }

    function setAddress(addr) {
        var el = document.getElementById('addr-input');
        if (el && addr) el.value = addr;
    }

    // -----------------------------------------------
    // 4. GEOCODIFICACIÓN INVERSA
    // -----------------------------------------------
    function reverseGeocode(lat, lng, callback) {
        var url = 'https://nominatim.openstreetmap.org/reverse'
                + '?format=jsonv2'
                + '&lat='             + lat
                + '&lon='             + lng
                + '&accept-language=es'
                + '&addressdetails=1';
        fetch(url, {
            headers: { 'Accept': 'application/json' },
            signal: AbortSignal.timeout(6000)
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            if (d && d.display_name) callback(formatAddress(d));
            else callback(null);
        })
        .catch(function () { callback(null); });
    }

    // -----------------------------------------------
    // 5. BÚSQUEDA CON AUTOCOMPLETADO
    // -----------------------------------------------
    function initAddressSearch() {
        var input    = document.getElementById('addr-input');
        var btn      = document.getElementById('btn-buscar-addr');
        var dropdown = document.getElementById('search-dropdown');
        if (!input) return;

        input.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            var q = input.value.trim();
            if (q.length < 3) { hideDropdown(); return; }
            searchTimeout = setTimeout(function () {
                doSearch(q, function (results) {
                    showDropdown(results, input, dropdown);
                });
            }, 400);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter')  { e.preventDefault(); hideDropdown(); triggerSearch(); }
            if (e.key === 'Escape') { hideDropdown(); }
        });

        input.addEventListener('blur', function () {
            if (dropdownMousedown) return;
            setTimeout(hideDropdown, 150);
        });

        if (btn) btn.addEventListener('click', function () {
            hideDropdown(); triggerSearch();
        });

        document.addEventListener('click', function (e) {
            if (!dropdownMousedown && dropdown &&
                !dropdown.contains(e.target) && e.target !== input) {
                hideDropdown();
            }
            dropdownMousedown = false;
        });
    }

    function triggerSearch() {
        var input = document.getElementById('addr-input');
        var btn   = document.getElementById('btn-buscar-addr');
        if (!input) return;
        var q = input.value.trim();
        if (!q) return;
        if (btn) { btn.textContent = '...'; btn.disabled = true; }
        doSearch(q, function (results) {
            if (btn) { btn.textContent = 'Buscar'; btn.disabled = false; }
            if (!results || !results.length) {
                showNoResults();
                return;
            }
            goToResult(results[0]);
        });
    }

    function doSearch(q, callback) {
        var query = q.trim();
        var queryBolivia = query;

        var interseccionMatch = query.match(
            /^(.+?)\s+(?:y|&|esq\.?|esquina)\s+(.+)$/i
        );

        if (interseccionMatch) {
            var calle1 = interseccionMatch[1].trim();
            var calle2 = interseccionMatch[2].trim();
            queryBolivia = calle1 + ' & ' + calle2 + ', Bolivia';
        } else if (query.toLowerCase().indexOf('bolivia') === -1) {
            queryBolivia = query + ', Bolivia';
        }

        var photonUrl = 'https://photon.komoot.io/api/?q='
            + encodeURIComponent(queryBolivia)
            + '&limit=6&lang=es';
        var nominatimUrl = 'https://nominatim.openstreetmap.org/search?format=json'
            + '&q='             + encodeURIComponent(queryBolivia)
            + '&limit=6&countrycodes=bo&accept-language=es&addressdetails=1';

        Promise.allSettled([
            fetch(photonUrl,    { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
            fetch(nominatimUrl, { headers: { 'Accept': 'application/json' } }).then(r => r.json())
        ]).then(function (results) {
            var photonData    = results[0].status === 'fulfilled' ? results[0].value : null;
            var nominatimData = results[1].status === 'fulfilled' ? results[1].value : null;
            var combined = [];

            if (photonData && photonData.features && photonData.features.length) {
                photonData.features.forEach(function (f) {
                    var p    = f.properties;
                    var name = [p.name, p.street, p.housenumber, p.city, p.county, p.state, p.country]
                               .filter(Boolean).join(', ');
                    combined.push({
                        display_name: name,
                        raw_name:     name,
                        lat: f.geometry.coordinates[1],
                        lon: f.geometry.coordinates[0]
                    });
                });
            }

            if (nominatimData && nominatimData.length) {
                nominatimData.forEach(function (r) {
                    var alreadyIn = combined.some(function (c) {
                        return Math.abs(parseFloat(c.lat) - parseFloat(r.lat)) < 0.001
                            && Math.abs(parseFloat(c.lon) - parseFloat(r.lon)) < 0.001;
                    });
                    if (!alreadyIn) {
                        combined.push({
                            display_name: formatAddress(r),
                            raw_name:     r.display_name,
                            lat: r.lat,
                            lon: r.lon
                        });
                    }
                });
            }

            callback(combined);
        });
    }

    function formatAddress(r) {
        var a = r.address || {};
        var parts = [];
        if (r.name && r.name !== a.road) parts.push(r.name);
        if (a.road) {
            parts.push(a.house_number ? a.road + ' ' + a.house_number : a.road);
        }
        if (a.neighbourhood || a.suburb) {
            parts.push(a.neighbourhood || a.suburb);
        }
        if (a.city || a.town || a.village || a.municipality) {
            parts.push(a.city || a.town || a.village || a.municipality);
        }
        if (a.state) parts.push(a.state);
        if (a.country) parts.push(a.country);
        return parts.filter(Boolean).join(', ') || r.display_name;
    }

    function showDropdown(results, input, dropdown) {
        if (!dropdown || !results || !results.length) { hideDropdown(); return; }
        dropdown.innerHTML = '';
        results.forEach(function (r) {
            var item = document.createElement('div');
            item.className = 'search-dropdown-item';
            item.innerHTML =
                '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1abc9c" stroke-width="2" style="flex-shrink:0;margin-top:2px">' +
                '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>' +
                '<circle cx="12" cy="9" r="2.5"/></svg>' +
                '<span>' + escapeHtml(r.display_name) + '</span>';

            item.addEventListener('mousedown', function (e) {
                dropdownMousedown = true;
                e.preventDefault();
            });
            item.addEventListener('mouseup', function () {
                input.value = r.display_name;
                hideDropdown();
                dropdownMousedown = false;
                goToResult(r);
            });
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    }

    function showNoResults() {
        var dropdown = document.getElementById('search-dropdown');
        if (!dropdown) return;
        dropdown.innerHTML =
            '<div style="padding:12px 14px;font-size:12px;color:#999;text-align:center;line-height:1.6">' +
            '⚠ No se encontraron resultados.<br>' +
            '<span style="font-size:11px">Intenta: "Av. Heroínas, Cochabamba" o arrastra el pin en el mapa</span>' +
            '</div>';
        dropdown.style.display = 'block';
    }

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function hideDropdown() {
        var d = document.getElementById('search-dropdown');
        if (d) d.style.display = 'none';
    }

    // -----------------------------------------------
    // FIX PRINCIPAL: goToResult
    // Actualiza coords ANTES de tocar el mapa,
    // y reutiliza el mapa si ya existe (no lo destruye)
    // -----------------------------------------------
    function goToResult(r) {
        var lat = parseFloat(r.lat);
        var lng = parseFloat(r.lon);
        var swAddr = document.getElementById('sw-addr');

        if (swAddr && !swAddr.checked) {
            swAddr.checked = true;
            updateFieldState(swAddr);
        }

        // PASO 1: actualizar coordenadas e inputs ANTES de cualquier operación con el mapa
        updateCoords(lat, lng);
        setAddress(r.display_name);

        var rawInput = document.getElementById('input-address-raw');
        if (rawInput) rawInput.value = r.raw_name || r.display_name;

        // PASO 2: si el mapa ya existe, solo moverlo — sin destruir ni recrear
        if (map && marker) {
            var placeholder = document.getElementById('map-placeholder');
            if (placeholder) placeholder.style.display = 'none';
            var mapDiv = document.getElementById('leaflet-map');
            if (mapDiv) mapDiv.style.display = 'block';
            map.setView([lat, lng], 16);
            marker.setLatLng([lat, lng]);
            if (mapModal && markerModal) {
                mapModal.setView([lat, lng], 16);
                markerModal.setLatLng([lat, lng]);
            }
            return;
        }

        // PASO 3: si el mapa no existe, mostrarlo
        // Las coords ya están en input-lat/lng y data-lat/lng,
        // buildMap() las leerá correctamente
        var placeholder = document.getElementById('map-placeholder');
        var mapDiv = document.getElementById('leaflet-map');
        if (placeholder) placeholder.style.display = 'none';
        if (mapDiv) mapDiv.style.display = 'block';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { buildMap(); });
        });
    }

    // -----------------------------------------------
    // 6. MODAL MAPA GRANDE
    // -----------------------------------------------
    function initModalMap() {
        var btnExpand = document.getElementById('btn-expand-map');
        var modal     = document.getElementById('map-modal');
        var btnClose  = document.getElementById('btn-close-modal');
        if (!btnExpand || !modal) return;

        if (modal.parentNode !== document.body) document.body.appendChild(modal);

        btnExpand.addEventListener('click', function () {
            modal.style.cssText = [
                'display:flex','position:fixed',
                'top:0','left:0','right:0','bottom:0',
                'z-index:99999','align-items:center','justify-content:center',
                'background:rgba(0,0,0,0.55)','padding:20px','box-sizing:border-box'
            ].join(';');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    setTimeout(buildModalMap, 50);
                });
            });
        });

        if (btnClose) btnClose.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    }

    function closeModal() {
        var modal = document.getElementById('map-modal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function buildModalMap() {
        var modalMapDiv = document.getElementById('leaflet-map-modal');
        if (!modalMapDiv || typeof L === 'undefined') return;

        var latEl = document.getElementById('input-lat');
        var lngEl = document.getElementById('input-lng');
        var lat = (latEl && latEl.value !== '') ? parseFloat(latEl.value) : -17.3895;
        var lng = (lngEl && lngEl.value !== '') ? parseFloat(lngEl.value) : -66.1568;
        if (isNaN(lat)) lat = -17.3895;
        if (isNaN(lng)) lng = -66.1568;

        if (mapModal) {
            mapModal.invalidateSize();
            mapModal.setView([lat, lng], 15);
            if (markerModal) markerModal.setLatLng([lat, lng]);
            return;
        }

        mapModal = L.map('leaflet-map-modal', { scrollWheelZoom: true, zoomControl: true })
                    .setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(mapModal);

        markerModal = L.marker([lat, lng], { icon: makeIcon(), draggable: true }).addTo(mapModal);

        markerModal.on('dragend', function (e) {
            var pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng, function (addr) {
                if (addr) setAddress(addr);
                if (marker) marker.setLatLng([pos.lat, pos.lng]);
            });
        });

        mapModal.on('click', function (e) {
            markerModal.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng, function (addr) {
                if (addr) setAddress(addr);
                if (marker) marker.setLatLng([e.latlng.lat, e.latlng.lng]);
            });
        });

        mapModal.whenReady(function () {
            setTimeout(function () { mapModal.invalidateSize(); }, 100);
        });
    }

    // -----------------------------------------------
    // 7. INIT al cargar si el switch ya está ON
    // -----------------------------------------------
    function initMapIfVisible() {
        var swAddr  = document.getElementById('sw-addr');
        var mapDiv  = document.getElementById('leaflet-map');
        if (!swAddr || !swAddr.checked || !mapDiv) return;

        var placeholder = document.getElementById('map-placeholder');
        if (placeholder) placeholder.style.display = 'none';
        mapDiv.style.display   = 'block';
        mapDiv.style.minHeight = mapDiv.style.minHeight || '260px';

        if (document.readyState === 'complete') {
            requestAnimationFrame(function () { requestAnimationFrame(buildMap); });
        } else {
            window.addEventListener('load', function () {
                requestAnimationFrame(function () { requestAnimationFrame(buildMap); });
            });
        }
    }

})();