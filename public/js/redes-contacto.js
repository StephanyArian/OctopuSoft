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
    // -----------------------------------------------
    function toggleMap(visible) {
        var placeholder = document.getElementById('map-placeholder');
        var mapDiv      = document.getElementById('leaflet-map');
        if (!placeholder || !mapDiv) return;

        if (visible) {
            placeholder.style.display = 'none';
            mapDiv.style.display      = 'block';
            if (map) { map.remove(); map = null; marker = null; }
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { buildMap(); });
            });
        } else {
            placeholder.style.display = 'flex';
            mapDiv.style.display      = 'none';
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

        var lat = parseFloat((document.getElementById('input-lat') || {}).value)
               || parseFloat(mapDiv.dataset.lat) || -17.3895;
        var lng = parseFloat((document.getElementById('input-lng') || {}).value)
               || parseFloat(mapDiv.dataset.lng) || -66.1568;

        if (map) {
            map.invalidateSize();
            map.setView([lat, lng], map.getZoom() || 15);
            if (marker) marker.setLatLng([lat, lng]);
            return;
        }

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
        var mapDiv = document.getElementById('leaflet-map');
        if (mapDiv) { mapDiv.dataset.lat = lat; mapDiv.dataset.lng = lng; }
    }

    function setAddress(addr) {
        var el = document.getElementById('addr-input');
        if (el && addr) el.value = addr;
    }

    // -----------------------------------------------
    // 4. GEOCODIFICACIÓN INVERSA
    //    — usa photon.komoot.io (no bloqueado, sin CORS)
    // -----------------------------------------------
    function reverseGeocode(lat, lng, callback) {
        // Photon no tiene reverse, usamos Nominatim con timeout largo
        // Si falla silenciosamente, no bloqueamos la UI
        var url = 'https://nominatim.openstreetmap.org/reverse'
                + '?format=jsonv2&lat=' + lat + '&lon=' + lng
                + '&accept-language=es';
        fetch(url, {
            headers: { 'Accept': 'application/json' },
            signal: AbortSignal.timeout(5000)
        })
        .then(function (r) { return r.json(); })
        .then(function (d) { callback(d && d.display_name ? d.display_name : null); })
        .catch(function () { callback(null); }); // falla silenciosamente
    }

    // -----------------------------------------------
    // 5. BÚSQUEDA CON AUTOCOMPLETADO
    //    — usa Photon (Komoot) en lugar de Nominatim
    //      para evitar bloqueos de red en local
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

    // ---- Photon API (Komoot) — sin bloqueo CORS, gratis ----
    function doSearch(q, callback) {
        // Bias hacia Bolivia/Cochabamba con bbox
        var query = encodeURIComponent(q);
        // lat/lon bias centrado en Bolivia
        var url = 'https://photon.komoot.io/api/?q=' + query
                + '&limit=5&lang=es'
                + '&lat=-17.3895&lon=-66.1568';  // bias Cochabamba
        fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var results = (data.features || []).map(function (f) {
                var p    = f.properties;
                var name = [p.name, p.street, p.city, p.state, p.country]
                           .filter(Boolean).join(', ');
                return {
                    display_name: name,
                    lat: f.geometry.coordinates[1],
                    lon: f.geometry.coordinates[0]
                };
            });
            callback(results);
        })
        .catch(function () { callback([]); });
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
            '<div style="padding:12px 14px;font-size:12px;color:#999;text-align:center;">' +
            'No se encontraron resultados. Intenta con más detalle.' +
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

    function goToResult(r) {
        var lat = parseFloat(r.lat);
        var lng = parseFloat(r.lon);
        var swAddr = document.getElementById('sw-addr');

        if (swAddr && !swAddr.checked) {
            swAddr.checked = true;
            updateFieldState(swAddr);
        }

        if (map) { map.remove(); map = null; marker = null; }
        toggleMap(true);
        setTimeout(function () {
            setAddress(r.display_name);
            updateCoords(lat, lng);
            if (map && marker) {
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
            }
            if (mapModal && markerModal) {
                mapModal.setView([lat, lng], 16);
                markerModal.setLatLng([lat, lng]);
            }
        }, 500);
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

        var lat = parseFloat((document.getElementById('input-lat') || {}).value) || -17.3895;
        var lng = parseFloat((document.getElementById('input-lng') || {}).value) || -66.1568;

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