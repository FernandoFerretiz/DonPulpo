<style>
.st-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.58); z-index: 9999;
    align-items: center; justify-content: center;
}
.st-overlay.active { display: flex; }
.st-dialog {
    background: #fff; border-radius: 18px; padding: 36px 28px 28px;
    max-width: 360px; width: calc(100% - 32px); text-align: center;
    box-shadow: 0 24px 64px rgba(0,0,0,.38);
    animation: st-pop .2s cubic-bezier(.34,1.56,.64,1);
}
@keyframes st-pop { from { transform: scale(.88); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.st-icon  { font-size: 2.4rem; line-height: 1; }
.st-title { margin: 10px 0 4px; font-size: 1.2rem; font-weight: 800; color: #1a202c; }
.st-sub   { color: #718096; font-size: .9rem; margin: 0; }
.st-count {
    font-size: 3.8rem; font-weight: 900; line-height: 1;
    margin: 14px 0 4px; color: #2b6cb0; transition: color .3s;
}
.st-count.danger { color: #e53e3e; }
.st-unit  { color: #a0aec0; font-size: .85rem; }
.st-actions { display: flex; gap: 10px; justify-content: center; margin-top: 22px; flex-wrap: wrap; }
.st-btn {
    padding: 11px 24px; border-radius: 9px; font-weight: 700;
    cursor: pointer; border: none; font-size: .95rem; transition: .15s ease;
}
.st-btn-keep { background: #2b6cb0; color: #fff; }
.st-btn-keep:hover { background: #2c5282; }
.st-btn-out  { background: #f7fafc; color: #4a5568; border: 1.5px solid #e2e8f0; }
.st-btn-out:hover { background: #edf2f7; }
</style>

<div id="st-overlay" class="st-overlay" role="alertdialog" aria-modal="true" aria-labelledby="st-title-txt">
    <div class="st-dialog">
        <div class="st-icon">⏳</div>
        <p class="st-title" id="st-title-txt">Sesión por expirar</p>
        <p class="st-sub">Tu sesión se cerrará en</p>
        <div id="st-count" class="st-count">60</div>
        <p class="st-unit">segundos</p>
        <div class="st-actions">
            <button id="st-keep" class="st-btn st-btn-keep">Mantener sesión</button>
            <button id="st-out"  class="st-btn st-btn-out">Cerrar sesión</button>
        </div>
    </div>
</div>

<form id="st-logout-form" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>

<script>
(function () {
    const LIFETIME_MS = {{ config('session.lifetime') }} * 60 * 1000;
    const WARN_BEFORE = 60 * 1000;
    const TOTAL_S     = 60;

    const overlay    = document.getElementById('st-overlay');
    const countEl    = document.getElementById('st-count');
    const btnKeep    = document.getElementById('st-keep');
    const btnOut     = document.getElementById('st-out');
    const logoutForm = document.getElementById('st-logout-form');

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
              || '{{ csrf_token() }}';

    let warnTimer, tickId;

    function doLogout() {
        clearInterval(tickId);
        logoutForm.submit();
    }

    function showWarning() {
        let remaining = TOTAL_S;
        countEl.textContent = remaining;
        countEl.classList.remove('danger');
        overlay.classList.add('active');

        tickId = setInterval(function () {
            remaining--;
            countEl.textContent = remaining;
            if (remaining <= 10) countEl.classList.add('danger');
            if (remaining <= 0) { clearInterval(tickId); doLogout(); }
        }, 1000);
    }

    function resetTimer() {
        clearTimeout(warnTimer);
        clearInterval(tickId);
        overlay.classList.remove('active');
        warnTimer = setTimeout(showWarning, LIFETIME_MS - WARN_BEFORE);
    }

    btnKeep.addEventListener('click', function () {
        fetch('{{ route('keep-alive') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        })
        .then(function (r) { r.ok ? resetTimer() : doLogout(); })
        .catch(doLogout);
    });

    btnOut.addEventListener('click', doLogout);

    resetTimer();
})();
</script>
