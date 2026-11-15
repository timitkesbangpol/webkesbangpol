@extends('dashboard.layouts.app')

@section('title', 'Struktur Organisasi')

@push('styles')
<style>
/* ---- Page Header ---- */
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; flex-wrap:wrap; gap:12px; }
.page-title { font-family:'Inter',sans-serif; font-size:1.25rem; font-weight:700; color:#1a1a2e; margin:0; }
.page-subtitle { font-size:0.8rem; color:#6c757d; margin:2px 0 0; }
.page-actions { display:flex; gap:8px; align-items:center; }
.btn-primary-red { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; background:linear-gradient(135deg,#B40D1A,#8a0a12); color:#fff; font-weight:600; font-size:0.82rem; border:none; border-radius:8px; cursor:pointer; text-decoration:none; box-shadow:0 3px 10px rgba(180,13,26,0.2); transition:all 0.2s; }
.btn-primary-red:hover { color:#fff; background:linear-gradient(135deg,#8a0a12,#5a060d); transform:translateY(-1px); }
.btn-outline { display:inline-flex; align-items:center; gap:5px; padding:7px 14px; background:#fff; color:#444; font-weight:500; font-size:0.8rem; border:1px solid #dde3ec; border-radius:7px; cursor:pointer; text-decoration:none; transition:all 0.15s; }
.btn-outline:hover { border-color:#B40D1A; color:#B40D1A; }
.btn-outline:disabled { opacity:0.4; cursor:not-allowed; }

/* ---- Toolbar ---- */
.toolbar-row { display:flex; align-items:center; gap:8px; margin-bottom:10px; flex-wrap:wrap; }
.toolbar-row .hint { font-size:0.6rem; color:#c8cdd8; margin-left:auto; white-space:nowrap; line-height:1; }
.toolbar-row .hint kbd { background:#f4f6f9; border:1px solid #e8eaee; border-radius:3px; padding:1px 4px; font-size:0.58rem; font-family:monospace; color:#c0c7d4; }
.tb-sep { width:1px; height:18px; background:#e0e4ea; }

/* ---- Tool Mode Buttons ---- */
.tool-mode-group { display:flex; gap:2px; background:#f0f2f5; border-radius:7px; padding:2px; }
.tool-mode-btn { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border:none; background:transparent; color:#555; font-size:0.78rem; font-weight:500; border-radius:5px; cursor:pointer; transition:all 0.15s; }
.tool-mode-btn.active { background:#fff; color:#B40D1A; font-weight:600; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
.tool-mode-btn:hover:not(.active) { background:rgba(255,255,255,0.6); }

/* ---- Canvas ---- */
.builder-body { position:relative; height:calc(100vh - 56px - 60px - 24px - 104px); overflow:hidden; cursor:default; background:#f8f9fc; border-radius:10px; border:1px solid #e8edf5; }
#canvas-grid-inline { position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:0; }
#canvas-stage-inline { position:absolute; top:0; left:0; width:4000px; height:3000px; transform-origin:0 0; will-change:transform; }
#svg-connectors-inline { position:absolute; top:0; left:0; width:100%; height:100%; z-index:1; overflow:visible; }


/* ---- Rubber-Band Selection ---- */
#rubber-band-select { position:absolute; pointer-events:none; z-index:50; border:1.5px solid #3b82f6; background:rgba(59,130,246,0.07); border-radius:3px; display:none; }
#rubber-band-select.show { display:block; }

/* ---- Panel garis: style group inline ---- */
.ct-group { display:flex; gap:2px; background:#f0f2f5; border-radius:8px; padding:2px; }
.ct-btn { border:none; background:transparent; color:#555; font-size:0.78rem; font-weight:600; padding:5px 10px; border-radius:6px; cursor:pointer; transition:all 0.12s; }
.ct-btn.active { background:#fff; color:#B40D1A; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
.ct-btn:hover:not(.active) { background:rgba(255,255,255,0.7); }
.ct-btn-danger { color:#dc3545; }
.ct-btn-danger:hover { background:#fdecea; }
.ct-select { width:100%; padding:6px 8px; border:1.5px solid #dde3ec; border-radius:6px; font-size:0.8rem; color:#1a1a2e; background:#f8fafd; cursor:pointer; outline:none; }
.ct-select:focus { border-color:#B40D1A; }

/* Panel garis color swatches */
.panel-garis-swatches { display:flex; gap:4px; flex-wrap:wrap; }
.panel-garis-swatches .cg-swatch { width:24px; height:24px; border-radius:5px; border:2.5px solid transparent; cursor:pointer; transition:all 0.12s; }
.panel-garis-swatches .cg-swatch:hover { transform:scale(1.15); }
.panel-garis-swatches .cg-swatch.active { border-color:#1a1a2e; box-shadow:0 0 0 2px #fff,0 0 0 4px #1a1a2e; }
.panel-garis-swatches .cg-reset { width:24px; height:24px; border-radius:5px; border:2px solid #dde3ec; cursor:pointer; background:#f4f6f9; font-size:0.75rem; color:#8892a8; display:flex; align-items:center; justify-content:center; transition:all 0.12s; }
.panel-garis-swatches .cg-reset:hover { border-color:#8892a8; background:#e8eaee; }

/* Panel disabled state (idle mode) */
.panel-section { display:none; }
.panel-section.active { display:block; }
.panel-disabled-overlay { pointer-events:none; opacity:0.45; }
.panel-idle-hint { text-align:center; padding:14px 8px 6px; color:#b0b8c9; font-size:0.75rem; line-height:1.6; }
.panel-idle-hint i { font-size:1.6rem; display:block; margin-bottom:6px; opacity:0.4; }
.panel-save-btn { width:100%; padding:8px; background:linear-gradient(135deg,#B40D1A,#8a0a12); color:#fff; font-weight:700; font-size:0.82rem; border:none; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 3px 10px rgba(180,13,26,0.2); transition:all 0.15s; }
.panel-save-btn:hover { background:linear-gradient(135deg,#8a0a12,#5a060d); transform:translateY(-1px); }
.panel-divider { border:none; border-top:1px solid #eef0f5; margin:8px 0; }

/* Floating color picker (keep for long-press) */
.connector-color-picker { position:fixed; z-index:1070; background:#fff; border:1px solid #e0e4ea; border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.15); padding:7px 10px; display:none; gap:4px; align-items:center; }
.connector-color-picker.show { display:flex; }
.connector-color-picker .cp-swatch { width:18px; height:18px; border-radius:4px; border:2px solid transparent; cursor:pointer; transition:all 0.12s; }
.connector-color-picker .cp-swatch:hover { transform:scale(1.25); }
.connector-color-picker .cp-swatch.active { border-color:#1a1a2e; box-shadow:0 0 0 1.5px #fff,0 0 0 3px #1a1a2e; }
.connector-color-picker .cp-reset { width:20px; height:20px; border-radius:4px; border:2px solid #dde3ec; cursor:pointer; background:#f4f6f9; font-size:0.7rem; color:#8892a8; display:flex; align-items:center; justify-content:center; transition:all 0.12s; margin-left:2px; }
.connector-color-picker .cp-reset:hover { border-color:#8892a8; background:#e8eaee; }

.builder-empty { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; color:#8892a8; z-index:0; pointer-events:none; }
.builder-empty i { font-size:2.8rem; display:block; margin-bottom:10px; opacity:0.4; }
.builder-empty p { font-size:0.9rem; margin:0; }

/* ---- Builder Node (Card Design matching Reference) ---- */
.builder-node {
    position:absolute; z-index:10; cursor:move; user-select:none;
    width:288px; height:96px; box-sizing:border-box;
    padding:0; border-radius:4px; background:#fff;
    border:1px solid #dde3ec;
    box-shadow:0 3px 14px rgba(0,0,0,0.10);
    transition:box-shadow 0.18s,border-color 0.15s;
    overflow:visible;
}
.builder-node:hover { box-shadow:0 6px 20px rgba(0,0,0,0.15); }
.builder-node.selected { border-color:var(--theme-color,#A91E23); box-shadow:0 0 0 3px rgba(169,30,35,0.18),0 8px 25px rgba(0,0,0,0.12); }
.builder-node.multi-selected { border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,0.25),0 6px 20px rgba(0,0,0,0.1); }
.builder-node.multi-selected.selected { border-color:var(--theme-color,#A91E23); box-shadow:0 0 0 3px rgba(169,30,35,0.18),0 8px 25px rgba(0,0,0,0.12); }
.builder-node.dragging { opacity:0.88; z-index:100; box-shadow:0 16px 45px rgba(0,0,0,0.22); }

/* Color themes */
.builder-node, .builder-node.color-blue { --theme-color:#2563eb; --theme-dark:#1e3a8a; --theme-light:#eff6ff; }
.builder-node.color-red   { --theme-color:#A91E23; --theme-dark:#58060a; --theme-light:#fff0f0; }
.builder-node.color-green { --theme-color:#16a34a; --theme-dark:#14532d; --theme-light:#f0fdf4; }
.builder-node.color-yellow{ --theme-color:#ca8a04; --theme-dark:#713f12; --theme-light:#fefce8; }
.builder-node.color-purple{ --theme-color:#9333ea; --theme-dark:#581c87; --theme-light:#faf5ff; }
.builder-node.color-orange{ --theme-color:#ea580c; --theme-dark:#9a3412; --theme-light:#fff7ed; }
.builder-node.color-teal  { --theme-color:#0d9488; --theme-dark:#115e59; --theme-light:#f0fdfa; }
.builder-node.color-pink  { --theme-color:#db2777; --theme-dark:#831843; --theme-light:#fdf2f8; }
.builder-node.color-gray  { --theme-color:#4b5563; --theme-dark:#1f2937; --theme-light:#f9fafb; }

/* Banner Ribbon on top-right */
.node-card-top {
    position:absolute;
    top:0;
    left:97px;
    right:-14px;
    height:30px;
    z-index:5;
}
.node-header-banner {
    width:100%;
    height:100%;
    background:var(--theme-color);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:0 14px 0 22px;
    box-sizing:border-box;
    clip-path:polygon(19px 100%, 0 0, 100% 0, 100% 100%);
    overflow:hidden;
}
.node-header-title {
    font-size:0.67rem;
    font-weight:800;
    letter-spacing:0.05em;
    text-transform:uppercase;
    color:#fff;
    text-shadow:0 1px 2px rgba(0,0,0,0.25);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    line-height:1.2;
    text-align:center;
}
/* 3D fold underneath the protruding ribbon */
.node-header-tail {
    position:absolute;
    right:0;
    top:30px;
    width:14px;
    height:13px;
    background:var(--theme-dark);
    clip-path:polygon(0 0, 100% 0, 0 100%);
}

/* Card body: photo on left, details on right */
.node-main-body {
    display:flex;
    flex-direction:row;
    align-items:center;
    padding:10px;
    box-sizing:border-box;
    width:100%;
    height:100%;
}
.node-photo-box {
    width:76px; min-width:76px; height:76px;
    border:2px solid var(--theme-color);
    background:#f8fafc;
    border-radius:2px;
    overflow:hidden;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    box-shadow:0 1px 4px rgba(0,0,0,0.06);
}
.node-photo-img {
    width:100%; height:100%;
    object-fit:cover;
    object-position:top center;
    display:block;
}
.node-photo-placeholder { font-size:1.9rem; color:#cbd5e1; }

/* Text details below the banner */
.node-details {
    flex:1; min-width:0;
    padding-left:11px;
    padding-top:26px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:2px;
    box-sizing:border-box;
}
.node-name {
    font-size:0.75rem;
    font-weight:800;
    color:var(--theme-color);
    line-height:1.2;
    margin-bottom:1px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
.node-name.empty { color:#94a3b8; font-style:italic; font-weight:500; }
.node-meta-row {
    font-size:0.58rem;
    line-height:1.35;
    color:#111827;
    display:flex;
    gap:3px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
.node-meta-lbl { font-weight:800; color:#111827; flex-shrink:0; }
.node-meta-val { font-weight:700; color:#111827; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Unit only */
.node-unit-center { display:flex; align-items:center; justify-content:center; text-align:center; padding:10px; width:100%; height:100%; box-sizing:border-box; }
.node-unit-title { font-size:0.74rem; font-weight:800; color:var(--theme-color); letter-spacing:0.02em; line-height:1.25; }

/* ---- Panel Photo Upload ---- */
.panel-photo-preview-wrap { display:flex; align-items:center; gap:12px; margin-bottom:8px; }
.panel-photo-preview { width:52px; height:52px; border-radius:8px; border:1.5px solid #dde3ec; background:#f8fafd; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
.panel-photo-img { width:100%; height:100%; object-fit:cover; object-position:top center; display:none; }
.panel-photo-placeholder { font-size:1.4rem; color:#b0b8c9; }

.node-delete-btn { position:absolute; top:-10px; right:-10px; width:22px; height:22px; background:#dc3545; color:#fff; border:2px solid #fff; border-radius:50%; cursor:pointer; font-size:0.65rem; display:none; align-items:center; justify-content:center; z-index:20; box-shadow:0 2px 6px rgba(0,0,0,0.15); transition:all 0.15s; line-height:1; }
.builder-node:hover .node-delete-btn { display:flex; }
.node-delete-btn:hover { transform:scale(1.15); background:#a71d2a; }

/* ---- SVG Waypoint Handles (Draw.io Style) ---- */
.wp-handle { fill:#fff; stroke:#3b82f6; stroke-width:2.5; cursor:grab; filter:drop-shadow(0 1px 4px rgba(0,0,0,0.2)); transition:all 0.1s; }
.wp-handle:hover { fill:#dbeafe; stroke-width:3; stroke:#2563eb; }
.wp-handle:active { cursor:grabbing; }
.wp-midpoint-handle { fill:#fff; stroke:#94a3b8; stroke-width:2; cursor:crosshair; opacity:0.5; filter:drop-shadow(0 1px 3px rgba(0,0,0,0.15)); transition:all 0.15s; }
.wp-midpoint-handle:hover { fill:#dbeafe; stroke:#2563eb; stroke-width:2.5; opacity:1; }
/* Draw.io style: selected connector glow */
.connector-path-selected { filter:drop-shadow(0 0 4px var(--connector-color, rgba(59,130,246,0.5))); }
/* Draw.io style: temporary connector during drag */
.connector-dragging { stroke:#3b82f6; stroke-width:2.5; stroke-dasharray:6,3; animation:connector-dash 0.5s linear infinite; fill:none; }
@keyframes connector-dash { to { stroke-dashoffset:-18; } }
/* Draw.io style: port handle arrows on nodes */
.node-port-arrow { position:absolute; width:16px; height:16px; display:flex; align-items:center; justify-content:center; z-index:16; cursor:crosshair; opacity:0; pointer-events:none; transition:opacity 0.12s, transform 0.12s, background 0.12s; border-radius:2px; background:transparent; }
.node-port-arrow svg { width:12px; height:12px; fill:none; stroke:#8892a8; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; transition:stroke 0.12s, transform 0.12s; }
.builder-node:hover .node-port-arrow, .builder-node.selected .node-port-arrow { opacity:1; pointer-events:auto; }
.node-port-arrow:hover { background:rgba(59,130,246,0.1); border-radius:50%; }
.node-port-arrow:hover svg { stroke:#2563eb; transform:scale(1.2); }
.node-port-arrow-t { top:-9px; left:50%; transform:translateX(-50%) rotate(0deg); }
.node-port-arrow-b { bottom:-9px; left:50%; transform:translateX(-50%) rotate(180deg); }
.node-port-arrow-l { left:-9px; top:50%; transform:translateY(-50%) rotate(-90deg); }
.node-port-arrow-r { right:-9px; top:50%; transform:translateY(-50%) rotate(90deg); }
.node-port-arrow-t:hover { transform:translateX(-50%) scale(1.15); }
.node-port-arrow-b:hover { transform:translateX(-50%) scale(1.15) rotate(180deg); }
.node-port-arrow-l:hover { transform:translateY(-50%) scale(1.15) rotate(-90deg); }
.node-port-arrow-r:hover { transform:translateY(-50%) scale(1.15) rotate(90deg); }

/* Draw.io style: selected line indicator */
.connector-hit-area { cursor:pointer; }
.connector-hit-area:hover { stroke:rgba(59,130,246,0.15); stroke-width:20; }

/* Draw.io style: endpoint handles at connector ends — HTML divs with high z-index */
.endpoint-handle-div { position:absolute; width:16px; height:16px; margin-left:-8px; margin-top:-8px; border-radius:50%; background:#fff; border:2.5px solid #3b82f6; z-index:25; cursor:grab; box-shadow:0 2px 8px rgba(0,0,0,0.25); transition:all 0.12s; pointer-events:auto; }
.endpoint-handle-div:hover { background:#dbeafe; border-color:#2563eb; transform:scale(1.2); }
.endpoint-handle-div:active { cursor:grabbing; background:#bfdbfe; }
.endpoint-handle-div-from { border-color:#059669; }
.endpoint-handle-div-from:hover { border-color:#047857; }
/* Draw.io style: port highlight when dragging endpoint near a node */
.port-highlight-oval { fill:rgba(59,130,246,0.2); stroke:#3b82f6; stroke-width:2; stroke-dasharray:4,2; pointer-events:none; animation:port-pulse 0.8s ease-in-out infinite alternate; }
@keyframes port-pulse { from { opacity:0.5; } to { opacity:1; } }
/* Draw.io style: temp line during endpoint drag */
.endpoint-temp-line { stroke:#3b82f6; stroke-width:2; stroke-dasharray:5,3; fill:none; pointer-events:none; opacity:0.8; transition:stroke-width 0.1s, opacity 0.1s; }
/* Magnetic snap indicator on temp line — hijau seperti draw.io */
.endpoint-temp-line-snapped { stroke:#22c55e !important; stroke-width:4 !important; opacity:1; stroke-dasharray:none; filter:drop-shadow(0 0 8px rgba(34,197,94,0.5)) drop-shadow(0 0 16px rgba(34,197,94,0.2)); }
/* Pulse animation for magnetic snap */
@keyframes magnet-pulse { 0% { rx:14; ry:14; opacity:0.3; } 50% { rx:22; ry:22; opacity:0.6; } 100% { rx:14; ry:14; opacity:0.3; } }
.port-highlight-oval { animation:port-pulse 0.8s ease-in-out infinite alternate; }
.port-highlight-oval.magnetized { animation:magnet-pulse 0.6s ease-in-out 2; fill:rgba(37,99,235,0.3); stroke:#2563eb; stroke-width:2.5; }

/* Draw.io / Figma style: alignment guide lines when dragging nodes & waypoints */
.align-guide { stroke:#ef4444; stroke-width:1.5; fill:none; pointer-events:none; stroke-dasharray:4,3; opacity:0.9; filter:drop-shadow(0 0 2px rgba(239,68,68,0.4)); }
.wp-align-guide { stroke:#3b82f6; stroke-width:1.5; fill:none; pointer-events:none; stroke-dasharray:4,3; opacity:0.9; filter:drop-shadow(0 0 2px rgba(59,130,246,0.4)); }

/* ---- Edit Panel ---- */
.builder-page-wrapper { padding-right:0; transition:none; }
.builder-layout-flex { display:flex; gap:0; align-items:flex-start; }
.builder-editor-col { flex:1; min-width:0; }
.builder-panel-inline { flex-shrink:0; width:300px; background:#fff; border-left:1px solid #e0e4ea; z-index:10; overflow-y:auto; padding:16px 14px; display:flex; flex-direction:column; gap:12px; box-shadow:-4px 0 20px rgba(0,0,0,0.06); position:sticky; top:0; max-height:calc(100vh - 60px); align-self:flex-start; border-radius:0 0 0 10px; }
.panel-title { font-size:0.82rem; font-weight:700; color:#1a1a2e; margin:0 0 2px; display:flex; align-items:center; gap:6px; }
.panel-title i { color:#B40D1A; }
.panel-subtitle { font-size:0.7rem; color:#8892a8; margin-bottom:6px; }
.panel-group { margin-bottom:10px; }
.panel-label { display:block; font-size:0.68rem; font-weight:600; color:#666; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px; }
.panel-input { width:100%; padding:6px 8px; border:1.5px solid #dde3ec; border-radius:6px; font-size:0.8rem; color:#1a1a2e; background:#f8fafd; outline:none; transition:border-color 0.15s; }
.panel-input:focus { border-color:#B40D1A; box-shadow:0 0 0 2px rgba(180,13,26,0.08); background:#fff; }
.panel-select { width:100%; padding:6px 8px; border:1.5px solid #dde3ec; border-radius:6px; font-size:0.8rem; color:#1a1a2e; background:#f8fafd; outline:none; cursor:pointer; }
.panel-select:focus { border-color:#B40D1A; }
.panel-colors { display:flex; gap:4px; flex-wrap:wrap; }
.panel-color-swatch { width:24px; height:24px; border-radius:5px; border:2.5px solid transparent; cursor:pointer; transition:all 0.12s; }
.panel-color-swatch:hover { transform:scale(1.15); }
.panel-color-swatch.active { border-color:#1a1a2e; box-shadow:0 0 0 2px #fff,0 0 0 4px #1a1a2e; }
.panel-coord { font-size:0.72rem; color:#8892a8; background:#f4f6f9; padding:5px 8px; border-radius:5px; }
.panel-btn-row { display:flex; gap:5px; margin-top:4px; }
.panel-btn { flex:1; padding:6px 8px; border-radius:6px; border:none; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; justify-content:center; gap:4px; }
.panel-btn-primary { background:#B40D1A; color:#fff; }
.panel-btn-primary:hover { background:#8a0a12; }
.panel-btn-danger { background:#fdecea; color:#dc3545; }
.panel-btn-danger:hover { background:#dc3545; color:#fff; }
.panel-btn-purple { background:#6366f1; color:#fff; }
.panel-btn-purple:hover { background:#4f46e5; }

.panel-multi-info { display:flex; align-items:center; gap:8px; padding:10px 12px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; font-size:0.8rem; color:#1d4ed8; }
.panel-multi-info i { font-size:1rem; flex-shrink:0; }
.shortcut-tip { font-size:0.65rem; color:#94a3b8; line-height:1.6; }
.shortcut-tip kbd { background:#f0f2f5; border:1px solid #dde3ec; border-radius:3px; padding:1px 4px; font-size:0.62rem; font-family:monospace; }

/* ---- Context Menu ---- */
.builder-context-menu-inline { position:fixed; z-index:1060; background:#fff; border:1px solid #e0e4ea; border-radius:8px; box-shadow:0 8px 30px rgba(0,0,0,0.12); padding:4px 0; min-width:160px; display:none; }
.builder-context-menu-inline.show { display:block; }
.ctx-item { padding:6px 12px; font-size:0.78rem; color:#333; cursor:pointer; display:flex; align-items:center; gap:7px; transition:background 0.1s; }
.ctx-item:hover { background:#f4f6f9; }
.ctx-item i { width:14px; text-align:center; color:#666; font-size:0.78rem; }
.ctx-sep { height:1px; background:#e0e4ea; margin:3px 0; }

/* ---- Toast ---- */
/* ---- Coordinate Tooltip (Segment Drag) ---- */
.coord-tooltip { position:fixed; z-index:9999; background:#1a1a2e; color:#fff; font-size:0.75rem;
  padding:5px 10px; border-radius:6px; display:none; align-items:center; gap:6px;
  pointer-events:none; box-shadow:0 4px 16px rgba(0,0,0,0.25);
  font-family:'Roboto Mono',monospace; white-space:nowrap; }
.coord-tooltip .coord-sep { width:1px; height:12px; background:rgba(255,255,255,0.2); }
.coord-tooltip strong { color:#22c55e; }

/* ---- Edit Waypoints Modal ---- */
.wp-modal-overlay { position:fixed; top:0; left:0; right:0; bottom:0; z-index:9998;
  background:rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center; }
.wp-modal-box { background:#fff; border-radius:12px; width:440px; max-width:90vw; max-height:80vh;
  display:flex; flex-direction:column; box-shadow:0 16px 60px rgba(0,0,0,0.2);
  animation:modalPop 0.2s ease; }
@keyframes modalPop { from { transform:scale(0.92); opacity:0; } to { transform:scale(1); opacity:1; } }
.wp-modal-header { display:flex; align-items:center; justify-content:space-between;
  padding:12px 16px; border-bottom:1px solid #eef0f5; font-size:0.9rem; font-weight:700; color:#1a1a2e; }
.wp-modal-header i { color:#B40D1A; margin-right:6px; }
.wp-modal-close { width:24px; height:24px; border:none; background:#f0f2f5; border-radius:6px;
  font-size:1.1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#666; }
.wp-modal-close:hover { background:#B40D1A; color:#fff; }
.wp-modal-body { padding:12px 16px; overflow-y:auto; flex:1; }
.wp-modal-hint { font-size:0.75rem; color:#8892a8; margin:0 0 10px; }
.wp-editor-row { display:flex; align-items:center; gap:6px; margin-bottom:6px;
  background:#f8f9fc; border-radius:6px; padding:6px 8px; border:1px solid #eef0f5; }
.wp-editor-row .wp-row-label { font-size:0.7rem; font-weight:600; color:#666; min-width:18px; }
.wp-editor-row .wp-row-input { width:60px; padding:3px 5px; border:1.5px solid #dde3ec;
  border-radius:4px; font-size:0.75rem; font-family:'Roboto Mono',monospace; outline:none; }
.wp-editor-row .wp-row-input:focus { border-color:#B40D1A; }
.wp-editor-row .wp-row-remove { margin-left:auto; border:none; background:transparent;
  color:#dc3545; cursor:pointer; font-size:0.8rem; padding:2px 5px; border-radius:4px; }
.wp-editor-row .wp-row-remove:hover { background:#fdecea; }
.wp-add-row-btn { display:flex; align-items:center; gap:4px; padding:5px 10px;
  background:#f0f2f5; border:1.5px dashed #dde3ec; border-radius:6px; font-size:0.75rem;
  color:#666; cursor:pointer; transition:all 0.12s; width:100%; justify-content:center; margin-top:4px; }
.wp-add-row-btn:hover { background:#e8eaee; border-color:#B40D1A; color:#B40D1A; }
.wp-modal-footer { display:flex; gap:6px; padding:10px 16px; border-top:1px solid #eef0f5; justify-content:flex-end; }
.wp-btn { padding:6px 14px; border-radius:6px; border:none; font-size:0.78rem; font-weight:600; cursor:pointer; transition:all 0.12s; }
.wp-btn-secondary { background:#f0f2f5; color:#444; }
.wp-btn-secondary:hover { background:#e0e4ea; }
.wp-btn-primary { background:#B40D1A; color:#fff; }
.wp-btn-primary:hover { background:#8a0a12; }

/* ---- Toast ---- */
.builder-toast-inline { position:fixed; bottom:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:5px; pointer-events:none; }
.builder-toast-item { pointer-events:auto; display:flex; align-items:center; gap:7px; padding:9px 14px; border-radius:8px; background:#1a1a2e; color:#fff; font-size:0.8rem; font-weight:500; box-shadow:0 6px 24px rgba(0,0,0,0.2); animation:slideInRight 0.3s ease; max-width:320px; }
.builder-toast-item.success i { color:#28a745; }
.builder-toast-item.error i { color:#dc3545; }
.builder-toast-item.info i { color:#0d6efd; }
@keyframes slideInRight { from { transform:translateX(100%); opacity:0; } to { transform:translateX(0); opacity:1; } }
</style>
@endpush

@section('content')
<div class="container-fluid builder-page-wrapper">
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Penyusun Struktur Organisasi</h1>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div style="background:#e8f7ee;border:1px solid #b2dfca;color:#1a6e3d;border-radius:8px;padding:10px 16px;margin-bottom:12px;font-size:0.85rem;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="toolbar-row">
        <button class="btn-primary-red" id="btn-tambah-data-inline">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
        <div class="tb-sep"></div>
        <button class="btn-outline" id="btn-undo-inline" disabled title="Undo (Ctrl+Z)"><i class="fas fa-undo"></i></button>
        <div class="tb-sep"></div>
        <button class="btn-outline" id="btn-save-layout-inline" style="background:#B40D1A;border-color:#B40D1A;color:#fff;">
            <i class="fas fa-save"></i> Simpan
        </button>
        <div class="tb-sep"></div>
        <button class="btn-outline" id="btn-zoom-fit-inline"><i class="fas fa-expand"></i> Fit</button>
        <button class="btn-outline" id="btn-zoom-reset-inline"><i class="fas fa-undo-alt"></i> Reset</button>
        <span style="font-size:0.75rem;color:#8892a8;min-width:38px;text-align:center;font-weight:500;" id="zoom-level-inline">100%</span>
        <div class="tb-sep"></div>
        <div class="tool-mode-group">
            <button class="tool-mode-btn active" id="tool-select-btn" title="Mode Pilih — klik &amp; drag banyak node (V)">
                <i class="fas fa-mouse-pointer"></i> Pilih
            </button>
            <button class="tool-mode-btn" id="tool-connect-btn" title="Mode Hubungkan — drag dari handle kotak (C)">
                <i class="fas fa-project-diagram"></i> Hubungkan
            </button>
        </div>
        <span class="hint">
            <kbd>Scroll</kbd>=Zoom &middot;
            <kbd>MMB</kbd>=Geser &middot;
            <kbd>V</kbd>/<kbd>C</kbd>=Mode &middot;
            <kbd>Ctrl+A</kbd>=Semua &middot;
            <kbd>Shift</kbd>+Klik=Multi &middot;
            <kbd>Del</kbd>=Hapus
        </span>
    </div>

    {{-- Builder Canvas + Panel (flex row) --}}
    <div class="builder-layout-flex">
        {{-- Editor Column --}}
        <div class="builder-editor-col">
            <div class="builder-body" id="builder-body-inline">
                <canvas id="canvas-grid-inline"></canvas>
                <div id="canvas-stage-inline">
                    <svg id="svg-connectors-inline"></svg>
                    @if($allStrukturors->isEmpty())
                    <div class="builder-empty" id="builder-empty-inline">
                        <i class="fas fa-sitemap"></i>
                        <p>Belum ada data. Tambah data terlebih dahulu, atau klik kanan pada kanvas.</p>
                    </div>
                    @endif
                </div>
                <div id="rubber-band-select"></div>
            </div>
        </div>

        {{-- Right Panel: 3 modes (idle / edit-kotak / edit-garis) --}}
        <div class="builder-panel-inline" id="builder-panel-inline">

            {{-- === MODE IDLE: tidak ada yang dipilih === --}}
            <div class="panel-section active" id="panel-section-idle">
                <div class="panel-idle-hint">
                    <i class="fas fa-mouse-pointer"></i>
                    Pilih <strong>kotak</strong> untuk mengedit data,<br>
                    atau <strong>garis</strong> untuk mengubah gaya koneksi.
                </div>
                <div class="panel-disabled-overlay">
                    <div class="panel-group">
                        <label class="panel-label">Nama</label>
                        <input class="panel-input" disabled placeholder="Nama lengkap">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">NIP</label>
                        <input class="panel-input" disabled placeholder="18 digit">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Jabatan</label>
                        <input class="panel-input" disabled placeholder="Nama jabatan">
                    </div>
                </div>
            </div>

            {{-- === MODE KOTAK === --}}
            <div class="panel-section" id="panel-section-kotak">
                <div>
                    <h3 class="panel-title"><i class="fas fa-square"></i> Edit Kotak</h3>
                    <p class="panel-subtitle">Perbarui data kotak yang dipilih.</p>
                </div>

                {{-- Multi-select --}}
                <div class="panel-multi-info" id="panel-multi-select-info" style="display:none;">
                    <i class="fas fa-object-group"></i>
                    <span><strong id="panel-multi-count">0</strong> kotak dipilih.<br>Geser untuk memindahkan semua bersama.</span>
                </div>

                {{-- Single-select fields --}}
                <div id="panel-single-fields">
                    <div class="panel-group">
                        <label class="panel-label">Nama</label>
                        <input class="panel-input" id="edit-nama-inline" placeholder="Nama lengkap">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">NIP</label>
                        <input class="panel-input" id="edit-nip-inline" placeholder="18 digit" maxlength="22">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Pangkat</label>
                        <input class="panel-input" id="edit-pangkat-inline" placeholder="Contoh: Pembina Utama Muda">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Golongan</label>
                        <input class="panel-input" id="edit-golongan-inline" placeholder="Contoh: IV c">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Jabatan</label>
                        <input class="panel-input" id="edit-jabatan-inline" placeholder="Nama jabatan">
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Atasan</label>
                        <select class="panel-select" id="edit-parent-inline">
                            <option value="">-- Root --</option>
                            @foreach($allStrukturors as $s)
                                <option value="{{ $s->id }}">{{ $s->jabatan }} {{ $s->nama !== '-' ? '- '.$s->nama : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Foto Pejabat / Pegawai</label>
                        <div class="panel-photo-preview-wrap">
                            <div class="panel-photo-preview" id="panel-photo-preview">
                                <i class="fas fa-user panel-photo-placeholder" id="panel-photo-placeholder"></i>
                                <img id="panel-photo-img" class="panel-photo-img" src="" alt="Preview">
                            </div>
                            <div style="flex:1;min-width:0;">
                                <input type="file" id="edit-image-inline" accept="image/jpeg,image/png,image/jpg,image/webp" style="display:none;">
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <button type="button" class="btn-outline" id="btn-upload-photo" style="padding:4px 9px;font-size:0.72rem;">
                                        <i class="fas fa-camera"></i> Pilih Foto
                                    </button>
                                    <button type="button" class="btn-outline" id="btn-remove-photo" style="padding:4px 9px;font-size:0.72rem;color:#dc3545;border-color:#f8d7da;display:none;">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                                <div style="font-size:0.65rem;color:#8892a8;margin-top:3px;">JPG, PNG, WEBP (Maks. 5MB)</div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-group">
                        <label class="panel-label">Warna Kotak</label>
                        <div class="panel-colors" id="edit-colors-inline">
                            <div class="panel-color-swatch active" data-color="blue" style="background:#3b82f6;" title="Biru"></div>
                            <div class="panel-color-swatch" data-color="red" style="background:#ef4444;" title="Merah"></div>
                            <div class="panel-color-swatch" data-color="green" style="background:#22c55e;" title="Hijau"></div>
                            <div class="panel-color-swatch" data-color="yellow" style="background:#eab308;" title="Kuning"></div>
                            <div class="panel-color-swatch" data-color="purple" style="background:#a855f7;" title="Ungu"></div>
                            <div class="panel-color-swatch" data-color="orange" style="background:#f97316;" title="Oranye"></div>
                            <div class="panel-color-swatch" data-color="teal" style="background:#14b8a6;" title="Teal"></div>
                            <div class="panel-color-swatch" data-color="pink" style="background:#ec4899;" title="Pink"></div>
                            <div class="panel-color-swatch" data-color="gray" style="background:#6b7280;" title="Abu"></div>
                        </div>
                    </div>
                    <div class="panel-coord">X: <span id="edit-x-inline">0</span> · Y: <span id="edit-y-inline">0</span></div>
                    <div class="panel-btn-row">
                        <button class="panel-btn panel-btn-primary" id="btn-update-node-inline"><i class="fas fa-check"></i> Perbarui</button>
                        <button class="panel-btn panel-btn-danger" id="btn-delete-node-inline"><i class="fas fa-trash"></i> Hapus</button>
                    </div>
                    <div class="panel-btn-row">
                        <button class="panel-btn panel-btn-purple" id="btn-duplicate-node-inline"><i class="fas fa-copy"></i> Duplikat</button>
                    </div>
                </div>
            </div>

            {{-- === MODE GARIS === --}}
            <div class="panel-section" id="panel-section-garis">
                <div>
                    <h3 class="panel-title"><i class="fas fa-project-diagram"></i> Edit Garis</h3>
                    <p class="panel-subtitle">Ubah gaya koneksi yang dipilih.</p>
                </div>

                <div class="panel-group">
                    <label class="panel-label">Bentuk Garis</label>
                    <div class="ct-group" id="ct-style-group">
                        <button class="ct-btn active" data-style="orthogonal" title="Siku"><i class="fas fa-project-diagram"></i> Siku</button>
                        <button class="ct-btn" data-style="straight" title="Lurus">— Lurus</button>
                        <button class="ct-btn" data-style="curved" title="Lengkung">~ Lengkung</button>
                    </div>
                </div>

                <div class="panel-group">
                    <label class="panel-label">Tipe Garis</label>
                    <div class="ct-group" id="ct-dash-group">
                        <button class="ct-btn active" data-dash="solid" title="Utuh">── Utuh</button>
                        <button class="ct-btn" data-dash="dashed" title="Putus-putus">- - Putus</button>
                    </div>
                </div>

                <div class="panel-group">
                    <label class="panel-label">Port Asal (Dari)</label>
                    <select class="ct-select" id="ct-from-port">
                        <option value="b">Bawah</option>
                        <option value="t">Atas</option>
                        <option value="r">Kanan</option>
                        <option value="l">Kiri</option>
                    </select>
                </div>

                <div class="panel-group">
                    <label class="panel-label">Port Tujuan (Ke)</label>
                    <select class="ct-select" id="ct-to-port">
                        <option value="t">Atas</option>
                        <option value="b">Bawah</option>
                        <option value="l">Kiri</option>
                        <option value="r">Kanan</option>
                    </select>
                </div>

                <div class="panel-group">
                    <label class="panel-label">Warna Garis</label>
                    <div class="panel-garis-swatches" id="ct-color-swatches">
                        <div class="cg-swatch" data-color="blue" style="background:#3b82f6;" title="Biru"></div>
                        <div class="cg-swatch" data-color="red" style="background:#ef4444;" title="Merah"></div>
                        <div class="cg-swatch" data-color="green" style="background:#22c55e;" title="Hijau"></div>
                        <div class="cg-swatch" data-color="yellow" style="background:#eab308;" title="Kuning"></div>
                        <div class="cg-swatch" data-color="purple" style="background:#a855f7;" title="Ungu"></div>
                        <div class="cg-swatch" data-color="orange" style="background:#f97316;" title="Oranye"></div>
                        <div class="cg-swatch" data-color="teal" style="background:#14b8a6;" title="Teal"></div>
                        <div class="cg-swatch" data-color="pink" style="background:#ec4899;" title="Pink"></div>
                        <div class="cg-swatch" data-color="gray" style="background:#6b7280;" title="Abu"></div>
                        <div class="cg-reset" id="cg-reset-btn" title="Reset warna">↺</div>
                    </div>
                </div>

                <div class="panel-btn-row">
                    <button class="panel-btn panel-btn-danger" id="ct-delete-btn"><i class="fas fa-trash"></i> Hapus Garis</button>
                </div>
            </div>

        </div>{{-- /builder-panel-inline --}}

    </div>{{-- /builder-layout-flex --}}
</div>{{-- /container-fluid --}}

{{-- Context Menu (Node & Connector & Waypoint) --}}
<div class="builder-context-menu-inline" id="context-menu-inline">
    {{-- Node items --}}
    <div class="ctx-node-item" data-action="add-child" style="display:none;"><i class="fas fa-plus-circle"></i> Tambah Anak</div>
    <div class="ctx-node-item" data-action="duplicate" style="display:none;"><i class="fas fa-copy"></i> Duplikat</div>
    <div class="ctx-sep ctx-node-sep" style="display:none;"></div>
    <div class="ctx-node-item" data-action="edit" style="display:none;"><i class="fas fa-pen"></i> Edit</div>
    <div class="ctx-node-item" data-action="delete" style="color:#dc3545;display:none;"><i class="fas fa-trash" style="color:#dc3545;"></i> Hapus</div>

    {{-- Connector items --}}
    <div class="ctx-connector-item" data-action="clear-waypoints" style="display:none;"><i class="fas fa-eraser"></i> Hapus Semua Belokan</div>
    <div class="ctx-connector-item" data-action="simplify" style="display:none;"><i class="fas fa-compress-arrows-alt"></i> Sederhanakan Garis</div>
    <div class="ctx-connector-item" data-action="edit-waypoints" style="display:none;"><i class="fas fa-sliders-h"></i> Edit Belokan</div>

    {{-- Waypoint item --}}
    <div class="ctx-waypoint-item" data-action="remove-wp" style="display:none;"><i class="fas fa-times-circle" style="color:#dc3545;"></i> Hapus Belokan Ini</div>
</div>

{{-- Coordinate Tooltip --}}
<div class="coord-tooltip" id="coord-tooltip-inline">
    <span id="coord-xy-inline">0, 0</span>
    <span class="coord-sep"></span>
    <span>Panjang: <strong id="coord-length-inline">0</strong>px</span>
</div>

{{-- Edit Waypoints Modal --}}
<div class="wp-modal-overlay" id="wp-modal-inline" style="display:none;">
    <div class="wp-modal-box">
        <div class="wp-modal-header">
            <span><i class="fas fa-sliders-h"></i> Edit Belokan Garis</span>
            <button class="wp-modal-close" id="wp-modal-close">&times;</button>
        </div>
        <div class="wp-modal-body" id="wp-modal-body">
            <p class="wp-modal-hint">Atur posisi titik belokan garis. Kosongkan untuk reset ke jalur otomatis.</p>
            <div id="wp-editor-list"></div>
        </div>
        <div class="wp-modal-footer">
            <button class="wp-btn wp-btn-secondary" id="wp-modal-cancel">Batal</button>
            <button class="wp-btn wp-btn-primary" id="wp-modal-save">Simpan</button>
        </div>
    </div>
</div>

{{-- Floating Color Picker for Connectors --}}
<div class="connector-color-picker" id="connector-color-picker-inline">
    <span class="cp-label">Garis</span>
    <div class="cp-swatch" data-color="blue" style="background:#3b82f6;" title="Biru"></div>
    <div class="cp-swatch" data-color="red" style="background:#ef4444;" title="Merah"></div>
    <div class="cp-swatch" data-color="green" style="background:#22c55e;" title="Hijau"></div>
    <div class="cp-swatch" data-color="yellow" style="background:#eab308;" title="Kuning"></div>
    <div class="cp-swatch" data-color="purple" style="background:#a855f7;" title="Ungu"></div>
    <div class="cp-swatch" data-color="orange" style="background:#f97316;" title="Oranye"></div>
    <div class="cp-swatch" data-color="teal" style="background:#14b8a6;" title="Teal"></div>
    <div class="cp-swatch" data-color="pink" style="background:#ec4899;" title="Pink"></div>
    <div class="cp-swatch" data-color="gray" style="background:#6b7280;" title="Abu"></div>
    <div class="cp-reset" id="cp-reset-inline" title="Reset ke warna parent">↺</div>
</div>



{{-- Toast --}}
<div class="builder-toast-inline" id="builder-toast-inline"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ===== STATE =====
    let nodes = @json($allStrukturors);
    let selectedNodeId  = null;       // primary selected node (for panel)
    let selectedNodeIds = new Set();  // all selected node IDs (multi-select)
    let zoomLevel = 1;
    let panX = 0, panY = 0;

    // Node drag
    let isDragging = false, dragNodeId = null;
    let dragOffsetX = 0, dragOffsetY = 0;
    let dragStartPositions = {};   // {nodeId: {x,y}} snapshot at drag-start

    // Connecting
    let isConnecting = false, connectFromId = null, tempLine = null;

    // Pan (middle mouse)
    let isPanning = false, panStartX = 0, panStartY = 0, panStartPanX = 0, panStartPanY = 0;

    // Rubber-band selection
    let isRubberBanding = false;
    let rbBodyStartX = 0, rbBodyStartY = 0;

    // Tool mode
    let toolMode = 'select'; // 'select' | 'connect'

    // Undo
    let undoStack = [];

    // Connector data
    const connectorWaypoints = new Map(); // key:"parentId-childId" → [{x,y}...]
    const connectorColors    = new Map(); // key:"parentId-childId" → colorName
    const connectorStyles    = new Map(); // key:"parentId-childId" → { type:'orthogonal'|'straight'|'curved', dash:'solid'|'dashed' }
    const connectorPorts     = new Map(); // key:"parentId-childId" → { fromPort:'b'|'t'|'l'|'r', toPort:'t'|'b'|'l'|'r' }
    let selectedConnectorKey = null;

    // Waypoint drag
    let isDraggingWaypoint = false;
    let draggingWpKey = null, draggingWpIdx = null;

    // Segment drag (drag □ midpoint → insert new waypoint)
    let isDraggingSegment = false;
    let draggingSegKey = null;
    let draggingSegInsertAt = null; // integer index, or -1 for 'default-mid'
    let segDragHasMoved = false;
    let segDragClientStart = { x: 0, y: 0 };
    let segDragStartedWp = false;

    // Connector long-press (color picker)
    let connectorHoldTimer = null, holdConnectorKey = null;

    // Endpoint drag (draw.io style: move endpoint to different port or different node)
    let isDraggingEndpoint = false;
    let draggingEndpointKey = null;
    let draggingEndpointSide = null; // 'from' or 'to'
    let endpointDragStartClientX = 0;
    let endpointDragStartClientY = 0;
    let endpointHoverPort = null;   // { nodeId, portName } during drag — nearest port
    let endpointTempLine = null;    // temp preview line during endpoint drag
    let endpointDragOrigFixedX = 0; // fixed point (the OTHER end of the connector)
    let endpointDragOrigFixedY = 0;

    // Alignment snap state (draw.io / Figma style — node-to-node magnetic alignment)
    let alignSnapX = null, alignSnapY = null; // snap offset to apply
    let alignGuides = []; // [{orient:'h'|'v', pos:number, start:number, end:number}]
    const ALIGN_SNAP = 18; // px threshold for magnetic alignment

    // Waypoint snap & smart guide state (draw.io style)
    let wpAlignSnapX = null, wpAlignSnapY = null;
    let wpAlignGuides = []; // same format as alignGuides
    const WP_ALIGN_SNAP = 18; // px threshold for waypoint alignment

    let tempIdCounter = -1;
    const SNAP = 20;
    const snapV = v => Math.round(v / SNAP) * SNAP;

    const COLOR_MAP = {
        blue:'#3b82f6', red:'#ef4444', green:'#22c55e', yellow:'#eab308',
        purple:'#a855f7', orange:'#f97316', teal:'#14b8a6', pink:'#ec4899', gray:'#6b7280'
    };

    // ===== DOM REFS =====
    const body         = document.getElementById('builder-body-inline');
    const stage        = document.getElementById('canvas-stage-inline');
    const svgEl        = document.getElementById('svg-connectors-inline');
    const emptyState   = document.getElementById('builder-empty-inline');
    const panel        = document.getElementById('builder-panel-inline');
    const contextMenu  = document.getElementById('context-menu-inline');
    const rubberBandEl = document.getElementById('rubber-band-select');
    const colorSwatches = document.querySelectorAll('#edit-colors-inline .panel-color-swatch');
    const ctxMenuOriginalHtml = contextMenu.innerHTML;

    // ===== HELPERS =====
    function clientToStage(cx, cy) {
        const r = body.getBoundingClientRect();
        return { x: (cx - r.left - panX) / zoomLevel, y: (cy - r.top - panY) / zoomLevel };
    }
    function bodyCoords(cx, cy) {
        const r = body.getBoundingClientRect();
        return { x: cx - r.left, y: cy - r.top };
    }
    function escHtml(s) {
        if (!s) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    function getNodeEl(id) { return stage.querySelector('.builder-node[data-id="' + id + '"]'); }
    function getNodeById(id) { return nodes.find(n => n.id === id); }
    function mkSvg(tag) { return document.createElementNS('http://www.w3.org/2000/svg', tag); }

    // ===== GRID =====
    const gridCanvas = document.getElementById('canvas-grid-inline');
    const gridCtx    = gridCanvas.getContext('2d');

    function resizeGrid() {
        const r = body.getBoundingClientRect();
        gridCanvas.width = r.width; gridCanvas.height = r.height;
        drawGrid();
    }
    function drawGrid() {
        if (!gridCtx) return;
        const w = gridCanvas.width, h = gridCanvas.height;
        if (!w || !h) return;
        gridCtx.clearRect(0, 0, w, h);
        const sp = SNAP * zoomLevel;
        const ox = ((panX % sp) + sp) % sp;
        const oy = ((panY % sp) + sp) % sp;
        gridCtx.fillStyle = 'rgba(0,0,0,0.065)';
        for (let x = ox; x < w; x += sp) {
            for (let y = oy; y < h; y += sp) {
                gridCtx.beginPath();
                gridCtx.arc(x, y, 1, 0, Math.PI * 2);
                gridCtx.fill();
            }
        }
    }

    // ===== TRANSFORM =====
    function applyTransform() {
        const cw = body.clientWidth, ch = body.clientHeight;
        const z = zoomLevel, mg = 200;
        let mnX = 0, mxX = 1500, mnY = 0, mxY = 1000;
        if (nodes.length > 0) {
            mnX = Math.min(...nodes.map(n => n.x || 0));
            mxX = Math.max(...nodes.map(n => (n.x || 0) + 288));
            mnY = Math.min(...nodes.map(n => n.y || 0));
            mxY = Math.max(...nodes.map(n => (n.y || 0) + 96));
        }
        // Clamp X
        const cWpx = (mxX - mnX) * z;
        if (cWpx <= cw) { panX = (cw - cWpx) / 2 - mnX * z; }
        else {
            const lo = -mg - mnX * z, hi = cw + mg - mxX * z;
            panX = lo > hi ? Math.max(hi, Math.min(lo, panX)) : Math.max(lo, Math.min(hi, panX));
        }
        // Clamp Y
        const cHpx = (mxY - mnY) * z;
        if (cHpx <= ch) { panY = (ch - cHpx) / 2 - mnY * z; }
        else {
            const lo = -mg - mnY * z, hi = ch + mg - mxY * z;
            panY = lo > hi ? Math.max(hi, Math.min(lo, panY)) : Math.max(lo, Math.min(hi, panY));
        }
        stage.style.transform = 'translate(' + panX + 'px,' + panY + 'px) scale(' + z + ')';
        document.getElementById('zoom-level-inline').textContent = Math.round(z * 100) + '%';
        drawGrid();
    }

    function setZoom(level, cx, cy) {
        const old = zoomLevel;
        zoomLevel = Math.max(0.3, Math.min(3, level));
        if (cx !== undefined && cy !== undefined) {
            panX = cx - (cx - panX) * (zoomLevel / old);
            panY = cy - (cy - panY) * (zoomLevel / old);
        }
        applyTransform();
    }

    function zoomFit() {
        if (nodes.length === 0) { zoomLevel = 1; panX = 0; panY = 0; applyTransform(); return; }
        const r = body.getBoundingClientRect();
        const mnX = Math.min(...nodes.map(n => n.x || 0));
        mxX = Math.max(...nodes.map(n => (n.x || 0) + 288));
        const mnY = Math.min(...nodes.map(n => n.y || 0));
        const mxY = Math.max(...nodes.map(n => (n.y || 0) + 96));
        const pad = 60;
        const s = Math.max(0.3, Math.min(1.5, Math.min(
            (r.width - pad * 2) / (mxX - mnX || 1),
            (r.height - pad * 2) / (mxY - mnY || 1)
        )));
        zoomLevel = s;
        panX = (r.width  - (mxX - mnX) * s) / 2 - mnX * s;
        panY = (r.height - (mxY - mnY) * s) / 2 - mnY * s;
        applyTransform();
    }

    // ===== ALIGNMENT GUIDE RENDER =====
    function renderAlignGuides() {
        // Remove old guides
        stage.querySelectorAll('.align-guide').forEach(function(g) { g.remove(); });
        // Draw new guides
        alignGuides.forEach(function(g) {
            var line = mkSvg('line');
            if (g.orient === 'v') {
                line.setAttribute('x1', g.pos); line.setAttribute('y1', g.start);
                line.setAttribute('x2', g.pos); line.setAttribute('y2', g.end);
            } else {
                line.setAttribute('x1', g.start); line.setAttribute('y1', g.pos);
                line.setAttribute('x2', g.end);   line.setAttribute('y2', g.pos);
            }
            line.setAttribute('class', 'align-guide');
            svgEl.appendChild(line);
        });
    }

    function clearAlignGuides() {
        stage.querySelectorAll('.align-guide').forEach(function(g) { g.remove(); });
        alignSnapX = null; alignSnapY = null; alignGuides = [];
    }

    // ===== WAYPOINT ALIGNMENT GUIDES (draw.io style) =====
    function renderWaypointGuides() {
        stage.querySelectorAll('.wp-align-guide').forEach(function(g) { g.remove(); });
        wpAlignGuides.forEach(function(g) {
            var line = mkSvg('line');
            if (g.orient === 'v') {
                line.setAttribute('x1', g.pos); line.setAttribute('y1', g.start);
                line.setAttribute('x2', g.pos); line.setAttribute('y2', g.end);
            } else {
                line.setAttribute('x1', g.start); line.setAttribute('y1', g.pos);
                line.setAttribute('x2', g.end);   line.setAttribute('y2', g.pos);
            }
            line.setAttribute('class', 'align-guide wp-align-guide');
            svgEl.appendChild(line);
        });
    }

    function clearWaypointGuides() {
        stage.querySelectorAll('.wp-align-guide').forEach(function(g) { g.remove(); });
        wpAlignSnapX = null; wpAlignSnapY = null; wpAlignGuides = [];
    }

    // ===== TOOLTIP KOORDINAT (draw.io style) =====
    var coordTooltip = document.getElementById('coord-tooltip-inline');
    var coordXY = document.getElementById('coord-xy-inline');
    var coordLength = document.getElementById('coord-length-inline');

    function showCoordTooltip(cx, cy, sx, sy, key) {
        // Calculate total path length
        var geom = getConnectorGeometry(key);
        var totalLen = 0;
        if (geom) {
            var wps = connectorWaypoints.get(key) || [];
            var pts = allPts(geom.x1, geom.y1, geom.x2, geom.y2, wps, geom.fd, geom.td, getConnectorStyle(key).type);
            for (var i = 1; i < pts.length; i++) {
                totalLen += Math.hypot(pts[i].x - pts[i-1].x, pts[i].y - pts[i-1].y);
            }
        }
        coordXY.textContent = Math.round(sx) + ', ' + Math.round(sy);
        coordLength.textContent = Math.round(totalLen);
        coordTooltip.style.display = 'flex';
        // Position near cursor, offset so it doesn't cover the cursor
        coordTooltip.style.left = (cx + 16) + 'px';
        coordTooltip.style.top = (cy - 36) + 'px';
    }

    function hideCoordTooltip() {
        coordTooltip.style.display = 'none';
    }

    // ===== EDIT WAYPOINTS MODAL (draw.io style) =====
    let wpModalKey = null; // key of connector being edited

    function openWaypointEditor(key) {
        wpModalKey = key;
        var wps = connectorWaypoints.get(key) || [];
        var list = document.getElementById('wp-editor-list');
        list.innerHTML = '';
        if (wps.length === 0) {
            list.innerHTML = '<p style="text-align:center;color:#8892a8;font-size:0.8rem;padding:10px 0;">Tidak ada belokan. Gunakan <strong>Drag titik ○</strong> pada garis untuk menambah.</p>';
        } else {
            wps.forEach(function(wp, idx) {
                addWpEditorRow(list, idx, wp.x, wp.y);
            });
        }
        // Add button
        var addBtn = document.createElement('div');
        addBtn.className = 'wp-add-row-btn';
        addBtn.innerHTML = '<i class="fas fa-plus"></i> Tambah Belokan';
        addBtn.addEventListener('click', function() {
            addWpEditorRow(list, -1, 0, 0);
        });
        list.appendChild(addBtn);
        document.getElementById('wp-modal-inline').style.display = 'flex';
    }

    function addWpEditorRow(list, idx, x, y) {
        var row = document.createElement('div');
        row.className = 'wp-editor-row';
        row.innerHTML =
            '<span class="wp-row-label">X</span>' +
            '<input class="wp-row-input" type="number" value="' + Math.round(x) + '" data-idx="' + idx + '" data-axis="x">' +
            '<span class="wp-row-label">Y</span>' +
            '<input class="wp-row-input" type="number" value="' + Math.round(y) + '" data-idx="' + idx + '" data-axis="y">' +
            '<button class="wp-row-remove" title="Hapus belokan ini"><i class="fas fa-times"></i></button>';
        row.querySelector('.wp-row-remove').addEventListener('click', function() {
            row.remove();
        });
        list.insertBefore(row, list.lastElementChild);
    }

    function closeWaypointEditor() {
        document.getElementById('wp-modal-inline').style.display = 'none';
        wpModalKey = null;
    }

    function saveWaypointEditor() {
        if (!wpModalKey) { closeWaypointEditor(); return; }
        var rows = document.querySelectorAll('#wp-editor-list .wp-editor-row');
        var newWps = [];
        rows.forEach(function(row) {
            var inputs = row.querySelectorAll('.wp-row-input');
            if (inputs.length === 2) {
                var vx = parseInt(inputs[0].value);
                var vy = parseInt(inputs[1].value);
                if (!isNaN(vx) && !isNaN(vy)) {
                    newWps.push({ x: snapV(vx), y: snapV(vy) });
                }
            }
        });
        pushUndo();
        if (newWps.length > 0) {
            connectorWaypoints.set(wpModalKey, newWps);
        } else {
            connectorWaypoints.delete(wpModalKey);
        }
        renderConnectors();
        persistConnectorData();
        showToast('Belokan diperbarui', 'success');
        closeWaypointEditor();
    }

    // Compute snap offset for a waypoint based on alignment to nodes, ports, sibling buses, and other waypoints
    function computeWaypointSnap(wx, wy, excludeKey) {
        var snapX = null, snapY = null;
        var minDiffX = WP_ALIGN_SNAP, minDiffY = WP_ALIGN_SNAP;
        var guides = [];

        // 1. Check all nodes (center X/Y, edges, and ports)
        nodes.forEach(function(node) {
            var r = getNodeRect(node);
            var cx = r.x + r.w / 2, cy = r.y + r.h / 2;
            var ports = getNodePorts(node);

            var xCands = [
                { pos: cx, start: Math.min(wy, r.y) - 30, end: Math.max(wy, r.y + r.h) + 30 },
                { pos: r.x, start: Math.min(wy, r.y) - 20, end: Math.max(wy, r.y + r.h) + 20 },
                { pos: r.x + r.w, start: Math.min(wy, r.y) - 20, end: Math.max(wy, r.y + r.h) + 20 }
            ];
            xCands.forEach(function(c) {
                var d = Math.abs(wx - c.pos);
                if (d < minDiffX) {
                    minDiffX = d;
                    snapX = c.pos - wx;
                    guides = guides.filter(function(g) { return g.orient !== 'v'; });
                    guides.push({ orient: 'v', pos: c.pos, start: c.start, end: c.end });
                }
            });

            var yCands = [
                { pos: cy, start: Math.min(wx, r.x) - 30, end: Math.max(wx, r.x + r.w) + 30 },
                { pos: r.y, start: Math.min(wx, r.x) - 20, end: Math.max(wx, r.x + r.w) + 20 },
                { pos: r.y + r.h, start: Math.min(wx, r.x) - 20, end: Math.max(wx, r.x + r.w) + 20 }
            ];
            yCands.forEach(function(c) {
                var d = Math.abs(wy - c.pos);
                if (d < minDiffY) {
                    minDiffY = d;
                    snapY = c.pos - wy;
                    guides = guides.filter(function(g) { return g.orient !== 'h'; });
                    guides.push({ orient: 'h', pos: c.pos, start: c.start, end: c.end });
                }
            });
        });

        // 2. Alignment to tree horizontal bus midY for all parent connectors
        nodes.forEach(function(n) {
            if (!n.parent_id) return;
            var parent = getNodeById(n.parent_id);
            if (!parent) return;
            var pr = getNodeRect(parent), cr = getNodeRect(n);
            var treeBusY = Math.round(((pr.y + pr.h) + cr.y) / 2);
            var dy = Math.abs(wy - treeBusY);
            if (dy < minDiffY) {
                minDiffY = dy;
                snapY = treeBusY - wy;
                guides = guides.filter(function(g) { return g.orient !== 'h'; });
                guides.push({ orient: 'h', pos: treeBusY, start: Math.min(wx, pr.x, cr.x) - 40, end: Math.max(wx, pr.x + pr.w, cr.x + cr.w) + 40 });
            }
        });

        // 3. Alignment to other waypoints
        connectorWaypoints.forEach(function(wps, key) {
            if (key === excludeKey) return;
            wps.forEach(function(wp) {
                var dx = Math.abs(wx - wp.x);
                if (dx < minDiffX) {
                    minDiffX = dx;
                    snapX = wp.x - wx;
                    guides = guides.filter(function(g) { return g.orient !== 'v'; });
                    guides.push({ orient: 'v', pos: wp.x, start: Math.min(wy, wp.y) - 20, end: Math.max(wy, wp.y) + 20 });
                }
                var dy = Math.abs(wy - wp.y);
                if (dy < minDiffY) {
                    minDiffY = dy;
                    snapY = wp.y - wy;
                    guides = guides.filter(function(g) { return g.orient !== 'h'; });
                    guides.push({ orient: 'h', pos: wp.y, start: Math.min(wx, wp.x) - 20, end: Math.max(wx, wp.x) + 20 });
                }
            });
        });

        return { snapX: snapX, snapY: snapY, guides: guides };
    }

    // ===== CONNECTOR MATH =====
    // Read actual node dimensions from DOM (pixel perfect, resistant to zoom scaling)
    function getNodeRect(node) {
        var el = getNodeEl(node.id);
        var w = 288, h = 96;
        if (el) {
            if (el.offsetWidth > 0)  w = el.offsetWidth;
            else if (el.getBoundingClientRect().width > 0) w = el.getBoundingClientRect().width / zoomLevel;
            if (el.offsetHeight > 0) h = el.offsetHeight;
            else if (el.getBoundingClientRect().height > 0) h = el.getBoundingClientRect().height / zoomLevel;
        }
        return { x: node.x || 0, y: node.y || 0, w: w, h: h };
    }

    // Get all 4 edge-center ports
    function getNodePorts(node) {
        var r = getNodeRect(node);
        return {
            t: { x: r.x + r.w / 2, y: r.y },
            b: { x: r.x + r.w / 2, y: r.y + r.h },
            l: { x: r.x,           y: r.y + r.h / 2 },
            r: { x: r.x + r.w,     y: r.y + r.h / 2 }
        };
    }

    // Auto-detect best port pair based on relative positions (Org Chart hierarchy)
    function getBestPorts(parentNode, childNode) {
        var pr = getNodeRect(parentNode), cr = getNodeRect(childNode);
        var pp = getNodePorts(parentNode), cp = getNodePorts(childNode);
        var pCy = pr.y + pr.h / 2, cCy = cr.y + cr.h / 2;
        var pCx = pr.x + pr.w / 2, cCx = cr.x + cr.w / 2;
        var dx = cCx - pCx;

        // In organizational charts:
        // Subordinates below parent always connect bottom->top (b -> t)
        if (cr.y >= pr.y + pr.h * 0.4) {
            return { from: pp.b, to: cp.t, fd: 'b', td: 't' };
        }
        // Nodes above parent connect top->bottom (t -> b)
        if (cr.y + cr.h <= pr.y + pr.h * 0.6) {
            return { from: pp.t, to: cp.b, fd: 't', td: 'b' };
        }
        // Same row / horizontal peers:
        if (dx >= 0) return { from: pp.r, to: cp.l, fd: 'r', td: 'l' };
        else         return { from: pp.l, to: cp.r, fd: 'l', td: 'r' };
    }

    function getConnectorStyle(key) {
        return connectorStyles.get(key) || { type: 'orthogonal', dash: 'solid' };
    }

    function getOrthogonalPts(x1, y1, x2, y2, fd, td, wps) {
        if (wps && wps.length > 0) {
            var pts = [{ x: x1, y: y1 }];
            for (var i = 0; i < wps.length; i++) {
                var last = pts[pts.length - 1];
                var wp = wps[i];
                if (i === 0) {
                    if (fd === 'b' || fd === 't') {
                        pts.push({ x: last.x, y: wp.y });
                        pts.push({ x: wp.x, y: wp.y });
                    } else {
                        pts.push({ x: wp.x, y: last.y });
                        pts.push({ x: wp.x, y: wp.y });
                    }
                } else {
                    pts.push({ x: last.x, y: wp.y });
                    pts.push({ x: wp.x, y: wp.y });
                }
            }
            var lastPt = pts[pts.length - 1];
            if (td === 't' || td === 'b') {
                pts.push({ x: lastPt.x, y: y2 });
                pts.push({ x: x2, y: y2 });
            } else {
                pts.push({ x: x2, y: lastPt.y });
                pts.push({ x: x2, y: y2 });
            }
            var clean = [];
            pts.forEach(function(p) {
                if (clean.length === 0 || Math.abs(clean[clean.length - 1].x - p.x) > 0.5 || Math.abs(clean[clean.length - 1].y - p.y) > 0.5) {
                    clean.push(p);
                }
            });
            return clean;
        }

        // Strict 90-degree orthogonal steps without custom waypoints
        if ((fd === 'b' || fd === 't') && (td === 't' || td === 'b')) {
            var midY = Math.round((y1 + y2) / 2);
            return [{ x: x1, y: y1 }, { x: x1, y: midY }, { x: x2, y: midY }, { x: x2, y: y2 }];
        }
        if ((fd === 'l' || fd === 'r') && (td === 'l' || td === 'r')) {
            var midX = Math.round((x1 + x2) / 2);
            return [{ x: x1, y: y1 }, { x: midX, y: y1 }, { x: midX, y: y2 }, { x: x2, y: y2 }];
        }
        if ((fd === 'b' || fd === 't') && (td === 'l' || td === 'r')) {
            return [{ x: x1, y: y1 }, { x: x1, y: y2 }, { x: x2, y: y2 }];
        }
        if ((fd === 'l' || fd === 'r') && (td === 't' || td === 'b')) {
            return [{ x: x1, y: y1 }, { x: x2, y: y1 }, { x: x2, y: y2 }];
        }
        return [{ x: x1, y: y1 }, { x: x1, y: y2 }, { x: x2, y: y2 }];
    }

    function buildPath(x1, y1, x2, y2, wps, fd, td, styleType) {
        styleType = styleType || 'orthogonal';

        if (styleType === 'straight') {
            if (wps && wps.length > 0) {
                var d = 'M ' + x1 + ' ' + y1;
                wps.forEach(function(wp) { d += ' L ' + wp.x + ' ' + wp.y; });
                return d + ' L ' + x2 + ' ' + y2;
            }
            return 'M ' + x1 + ' ' + y1 + ' L ' + x2 + ' ' + y2;
        }

        if (styleType === 'curved') {
            if (wps && wps.length === 1) {
                return 'M ' + x1 + ' ' + y1 + ' Q ' + wps[0].x + ' ' + wps[0].y + ' ' + x2 + ' ' + y2;
            }
            var dx = x2 - x1, dy = y2 - y1;
            var cx1 = x1, cy1 = y1 + dy * 0.5;
            var cx2 = x2, cy2 = y2 - dy * 0.5;
            if (fd === 'r' || fd === 'l') { cx1 = x1 + dx * 0.5; cy1 = y1; }
            if (td === 'r' || td === 'l') { cx2 = x2 - dx * 0.5; cy2 = y2; }
            return 'M ' + x1 + ' ' + y1 + ' C ' + cx1 + ' ' + cy1 + ', ' + cx2 + ' ' + cy2 + ', ' + x2 + ' ' + y2;
        }

        // Strict Orthogonal: 100% Horizontal & Vertical steps only!
        var pts = getOrthogonalPts(x1, y1, x2, y2, fd, td, wps);
        var res = 'M ' + pts[0].x + ' ' + pts[0].y;
        for (var i = 1; i < pts.length; i++) {
            res += ' L ' + pts[i].x + ' ' + pts[i].y;
        }
        return res;
    }

    function allPts(x1, y1, x2, y2, wps, fd, td, styleType) {
        styleType = styleType || 'orthogonal';
        if (styleType === 'straight' || styleType === 'curved') {
            if (wps && wps.length > 0) return [{ x: x1, y: y1 }].concat(wps, [{ x: x2, y: y2 }]);
            return [{ x: x1, y: y1 }, { x: (x1 + x2) / 2, y: (y1 + y2) / 2 }, { x: x2, y: y2 }];
        }
        return getOrthogonalPts(x1, y1, x2, y2, fd, td, wps);
    }

    // ===== RENDER NODES =====
    function renderNodes() {
        stage.querySelectorAll('.builder-node').forEach(el => el.remove());
        if (nodes.length === 0) {
            if (emptyState) emptyState.style.display = 'block';
            renderConnectors(); return;
        }
        if (emptyState) emptyState.style.display = 'none';        var arrowSvg = '<svg viewBox="0 0 12 12"><path d="M6 1 L11 6 L6 11 L1 6 Z" fill="none" stroke-width="1.8" stroke-linejoin="round"/></svg>';
        nodes.forEach(node => {
            const el = document.createElement('div');
            const colorName = node.color || 'blue';
            el.className = 'builder-node color-' + colorName;
            if (node.id === selectedNodeId)      el.classList.add('selected');
            if (selectedNodeIds.has(node.id) && selectedNodeIds.size > 1) el.classList.add('multi-selected');
            el.dataset.id = node.id;
            el.style.left = (node.x || 0) + 'px';
            el.style.top  = (node.y || 0) + 'px';

            const hasPhoto = node.foto_profile && String(node.foto_profile).trim() !== '';
            const photoUrl = hasPhoto ? ('{{ asset('images/struktur-organisasi') }}/' + encodeURIComponent(node.foto_profile)) : '';
            const hasName = node.nama && node.nama !== '-';
            const hasNip = node.nip && node.nip !== '-';
            const isKelompokUnit = !hasPhoto && !hasName && !hasNip && String(node.jabatan || '').toUpperCase().includes('KELOMPOK');

            let golText = '-';
            if (node.pangkat && node.pangkat !== '-' && node.golongan && node.golongan !== '-') {
                golText = escHtml(node.pangkat) + ' / ' + escHtml(node.golongan);
            } else if (node.golongan && node.golongan !== '-') {
                golText = escHtml(node.golongan);
            } else if (node.pangkat && node.pangkat !== '-') {
                golText = escHtml(node.pangkat);
            }

            let cardContent = '';
            if (isKelompokUnit) {
                cardContent =
                    '<div class="node-card-top">' +
                        '<div class="node-header-banner">' +
                            '<div class="node-header-title" title="' + escHtml(node.jabatan) + '">' + escHtml(node.jabatan) + '</div>' +
                        '</div>' +
                        '<div class="node-header-tail"></div>' +
                    '</div>' +
                    '<div class="node-main-body node-unit-center">' +
                        '<div class="node-unit-title">' + escHtml(node.jabatan) + '</div>' +
                    '</div>';
            } else {
                let photoHtml = '';
                if (hasPhoto) {
                    photoHtml = '<img src="' + photoUrl + '" class="node-photo-img" alt="Foto" onerror="this.onerror=null;this.parentElement.innerHTML=\'<i class=\\\'fas fa-user node-photo-placeholder\\\'></i>\';">';
                } else {
                    photoHtml = '<i class="fas fa-user node-photo-placeholder"></i>';
                }

                cardContent =
                    '<div class="node-card-top">' +
                        '<div class="node-header-banner">' +
                            '<div class="node-header-title" title="' + escHtml(node.jabatan) + '">' + escHtml(node.jabatan) + '</div>' +
                        '</div>' +
                        '<div class="node-header-tail"></div>' +
                    '</div>' +
                    '<div class="node-main-body">' +
                        '<div class="node-photo-box">' + photoHtml + '</div>' +
                        '<div class="node-details">' +
                            '<div class="node-name' + (!hasName ? ' empty' : '') + '">' +
                                (hasName ? escHtml(node.nama) : '[Nama Belum Diisi]') +
                            '</div>' +
                            '<div class="node-meta-row">' +
                                '<span class="node-meta-lbl">NIP &nbsp;:</span>' +
                                '<span class="node-meta-val">' + (hasNip ? escHtml(node.nip) : '-') + '</span>' +
                            '</div>' +
                            '<div class="node-meta-row">' +
                                '<span class="node-meta-lbl">Golongan :</span>' +
                                '<span class="node-meta-val">' + golText + '</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
            }

            el.innerHTML =
                cardContent +
                '<div class="node-port-arrow node-port-arrow-t" data-id="' + node.id + '">' + arrowSvg + '</div>' +
                '<div class="node-port-arrow node-port-arrow-b" data-id="' + node.id + '">' + arrowSvg + '</div>' +
                '<div class="node-port-arrow node-port-arrow-l" data-id="' + node.id + '">' + arrowSvg + '</div>' +
                '<div class="node-port-arrow node-port-arrow-r" data-id="' + node.id + '">' + arrowSvg + '</div>' +
                '<button class="node-delete-btn" data-id="' + node.id + '">×</button>';

            el.addEventListener('dblclick', function(e) {
                if (e.target.closest('.node-port-arrow')) return;
                selectNode(node.id, false);
                panel.classList.add('open');
                document.getElementById('edit-jabatan-inline').focus();
            });

            el.addEventListener('mousedown', function(e) {
                if (e.target.closest('.node-delete-btn') || e.target.closest('.node-port-arrow')) return;
                if (e.button !== 0) return;
                e.stopPropagation();
                if (e.shiftKey) {
                    toggleNodeInSelection(node.id);
                } else {
                    if (!selectedNodeIds.has(node.id)) selectNode(node.id, false);
                    else if (selectedNodeId !== node.id) { selectedNodeId = node.id; updatePanel(node.id); }
                    startDrag(e, node.id);
                }
            });

            el.addEventListener('contextmenu', function(e) {
                e.preventDefault(); e.stopPropagation();
                selectNode(node.id, false);
                showContextMenu(e.clientX, e.clientY, node.id);
            });

            stage.appendChild(el);
        });

        stage.querySelectorAll('.node-port-arrow').forEach(function(handle) {
            handle.addEventListener('mousedown', function(e) {
                e.preventDefault(); e.stopPropagation();
                var port = 'b';
                if (handle.classList.contains('node-port-arrow-t')) port = 't';
                if (handle.classList.contains('node-port-arrow-b')) port = 'b';
                if (handle.classList.contains('node-port-arrow-l')) port = 'l';
                if (handle.classList.contains('node-port-arrow-r')) port = 'r';
                startConnect(e, parseInt(handle.dataset.id), port);
            });
        });

        // Tombol X: hapus kotak langsung
        stage.querySelectorAll('.node-delete-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const id = parseInt(btn.dataset.id);
                deleteNode(id);
            });
        });

        renderConnectors();
    }

    // ===== RENDER CONNECTORS + WAYPOINT HANDLES =====
    function renderConnectors() {
        svgEl.innerHTML = '';

        var handleQueue = [];

        nodes.forEach(function(node) {
            if (!node.parent_id) return;
            var parent = getNodeById(node.parent_id);
            if (!parent) return;

            var key = parent.id + '-' + node.id;
            var wps = connectorWaypoints.get(key) || [];
            var baseColor = COLOR_MAP[parent.color] || '#8892a8';
            var finalColor = connectorColors.has(key) ? (COLOR_MAP[connectorColors.get(key)] || baseColor) : baseColor;
            var isSelected = key === selectedConnectorKey;
            var cStyle = getConnectorStyle(key);

            // Connection Ports (user explicit choice or auto-detect)
            var pp = getNodePorts(parent), cp = getNodePorts(node);
            var userPorts = connectorPorts.get(key);
            var x1, y1, x2, y2, fd, td;

            if (userPorts && userPorts.fromPort && userPorts.toPort) {
                fd = userPorts.fromPort;
                td = userPorts.toPort;
                x1 = pp[fd].x; y1 = pp[fd].y;
                x2 = cp[td].x; y2 = cp[td].y;
            } else {
                var autoPorts = getBestPorts(parent, node);
                x1 = autoPorts.from.x; y1 = autoPorts.from.y;
                x2 = autoPorts.to.x;   y2 = autoPorts.to.y;
                fd = autoPorts.fd;     td = autoPorts.td;
            }

            var d = buildPath(x1, y1, x2, y2, wps, fd, td, cStyle.type);

            // Shadow
            var sh = mkSvg('path');
            sh.setAttribute('d', d); sh.setAttribute('stroke', 'rgba(0,0,0,0.05)');
            sh.setAttribute('stroke-width', '7'); sh.setAttribute('fill', 'none');
            sh.setAttribute('stroke-linecap', 'round'); sh.setAttribute('stroke-linejoin', 'round');
            sh.style.pointerEvents = 'none';
            svgEl.appendChild(sh);

            // Visual path
            var vp = mkSvg('path');
            vp.setAttribute('d', d); vp.setAttribute('stroke', finalColor);
            vp.setAttribute('stroke-width', isSelected ? '3' : '2');
            vp.setAttribute('fill', 'none');
            vp.setAttribute('stroke-linecap', 'round'); vp.setAttribute('stroke-linejoin', 'round');
            if (cStyle.dash === 'dashed') {
                vp.setAttribute('stroke-dasharray', '6,4');
            }
            if (isSelected) {
                vp.setAttribute('stroke-width', '3.5');
                // Draw.io style: multi-layered glow on selected connector
                vp.style.filter = 'drop-shadow(0 0 3px ' + finalColor + '99) drop-shadow(0 0 8px ' + finalColor + '44)';
                vp.style.transition = 'none';
            }
            if (isDraggingEndpoint && key === draggingEndpointKey) {
                vp.style.opacity = '0.2';
                sh.style.opacity = '0';
            }
            vp.style.pointerEvents = 'none';
            svgEl.appendChild(vp);

            // Draw.io style: Arrowhead at child (target) end
            var arrowPoly = drawArrowhead(x2, y2, td, isSelected ? 2.5 : 1.8, finalColor);
            if (arrowPoly) {
                if (isDraggingEndpoint && key === draggingEndpointKey) arrowPoly.style.opacity = '0.2';
                svgEl.appendChild(arrowPoly);
            }

            // Hit area (transparent wide path for click events) — draw.io style hover
            var hp = mkSvg('path');
            hp.setAttribute('d', d); hp.setAttribute('stroke', 'transparent');
            hp.setAttribute('stroke-width', '20'); hp.setAttribute('fill', 'none');
            hp.setAttribute('data-connector-hit', key);
            hp.setAttribute('class', 'connector-hit-area');
            svgEl.appendChild(hp);

            // Draw.io style: faint hover indication path (hidden, shown on hover via CSS)
            var hoverGlow = mkSvg('path');
            hoverGlow.setAttribute('d', d);
            hoverGlow.setAttribute('stroke', 'transparent');
            hoverGlow.setAttribute('stroke-width', '12');
            hoverGlow.setAttribute('fill', 'none');
            hoverGlow.setAttribute('data-connector-glow', key);
            hoverGlow.style.pointerEvents = 'none';
            hoverGlow.style.transition = 'stroke 0.15s';
            svgEl.appendChild(hoverGlow);

            if (isSelected) {
                handleQueue.push({ key: key, x1: x1, y1: y1, x2: x2, y2: y2, wps: wps, fd: fd, td: td, styleType: cStyle.type });
            }
        });

        handleQueue.forEach(function(h) {
            renderWaypointHandles(h.key, h.x1, h.y1, h.x2, h.y2, h.wps, h.fd, h.td, h.styleType);
        });

        // Draw.io style: render endpoint handles for selected connector — HTML divs with high z-index
        var oldEpDivs = stage.querySelectorAll('.endpoint-handle-div');
        oldEpDivs.forEach(function(d) { d.remove(); });
        if (selectedConnectorKey && (!isDraggingEndpoint || selectedConnectorKey !== draggingEndpointKey)) {
            var epKey = selectedConnectorKey;
            var epParts = epKey.split('-');
            if (epParts.length === 2) {
                var epParentId = parseInt(epParts[0]), epChildId = parseInt(epParts[1]);
                var epParent = getNodeById(epParentId), epChild = getNodeById(epChildId);
                if (epParent && epChild) {
                    var epPP = getNodePorts(epParent), epCP = getNodePorts(epChild);
                    var epUserPorts = connectorPorts.get(epKey);
                    var epFd, epTd, epX1, epY1, epX2, epY2;
                    if (epUserPorts && epUserPorts.fromPort && epUserPorts.toPort) {
                        epFd = epUserPorts.fromPort; epTd = epUserPorts.toPort;
                        epX1 = epPP[epFd].x; epY1 = epPP[epFd].y;
                        epX2 = epCP[epTd].x; epY2 = epCP[epTd].y;
                    } else {
                        var epAuto = getBestPorts(epParent, epChild);
                        epX1 = epAuto.from.x; epY1 = epAuto.from.y;
                        epX2 = epAuto.to.x;   epY2 = epAuto.to.y;
                        epFd = epAuto.fd;      epTd = epAuto.td;
                    }
                    // From endpoint (parent side) — green border
                    var epFrom = document.createElement('div');
                    epFrom.className = 'endpoint-handle-div endpoint-handle-div-from';
                    epFrom.dataset.epKey = epKey;
                    epFrom.dataset.epSide = 'from';
                    epFrom.dataset.epNode = String(epParentId);
                    epFrom.dataset.epPort = epFd;
                    epFrom.style.left = epX1 + 'px';
                    epFrom.style.top  = epY1 + 'px';
                    stage.appendChild(epFrom);
                    // To endpoint (child side) — blue border
                    var epTo = document.createElement('div');
                    epTo.className = 'endpoint-handle-div';
                    epTo.dataset.epKey = epKey;
                    epTo.dataset.epSide = 'to';
                    epTo.dataset.epNode = String(epChildId);
                    epTo.dataset.epPort = epTd;
                    epTo.style.left = epX2 + 'px';
                    epTo.style.top  = epY2 + 'px';
                    stage.appendChild(epTo);
                }
            }
        }
    }

    // Draw.io style: Arrowhead at connector end
    function drawArrowhead(x, y, portDir, strokeW, color) {
        var angle = 0;
        if (portDir === 'b') angle = Math.PI / 2;
        else if (portDir === 't') angle = -Math.PI / 2;
        else if (portDir === 'l') angle = Math.PI;
        else if (portDir === 'r') angle = 0;
        var len = 10, ang = 0.42;
        var pArr = [
            (x - len * Math.cos(angle - ang)) + ',' + (y - len * Math.sin(angle - ang)),
            x + ',' + y,
            (x - len * Math.cos(angle + ang)) + ',' + (y - len * Math.sin(angle + ang))
        ];
        var poly = mkSvg('polygon');
        poly.setAttribute('points', pArr.join(' '));
        poly.setAttribute('fill', color);
        poly.setAttribute('stroke', 'none');
        poly.style.pointerEvents = 'none';
        poly.style.opacity = '0.85';
        return poly;
    }

    // Helper: get connector geometry from key
    function getConnectorGeometry(key) {
        var parts = key.split('-');
        if (parts.length < 2) return null;
        var parentId = parseInt(parts[0]), childId = parseInt(parts[1]);
        var parentNode = getNodeById(parentId), childNode = getNodeById(childId);
        if (!parentNode || !childNode) return null;
        var pp = getNodePorts(parentNode), cp = getNodePorts(childNode);
        var userPorts = connectorPorts.get(key);
        var x1, y1, x2, y2, fd, td;
        if (userPorts && userPorts.fromPort && userPorts.toPort) {
            fd = userPorts.fromPort; td = userPorts.toPort;
            x1 = pp[fd].x; y1 = pp[fd].y;
            x2 = cp[td].x; y2 = cp[td].y;
        } else {
            var auto = getBestPorts(parentNode, childNode);
            x1 = auto.from.x; y1 = auto.from.y;
            x2 = auto.to.x;   y2 = auto.to.y;
            fd = auto.fd;      td = auto.td;
        }
        return { x1: x1, y1: y1, x2: x2, y2: y2, fd: fd, td: td };
    }


    function renderWaypointHandles(key, x1, y1, x2, y2, wps, fd, td, styleType) {
        if (isDraggingEndpoint && draggingEndpointKey === key) return;
        var hasWps = wps && wps.length > 0;
        var pts = allPts(x1, y1, x2, y2, wps, fd, td, styleType);

        // Draw.io style: ○ Small circle midpoint handles on each segment
        for (var i = 0; i < pts.length - 1; i++) {
            var a = pts[i], b = pts[i + 1];
            if (Math.hypot(b.x - a.x, b.y - a.y) < 16) continue;
            var mx = (a.x + b.x) / 2, my = (a.y + b.y) / 2;
            var circ = mkSvg('circle');
            circ.setAttribute('cx', mx); circ.setAttribute('cy', my);
            circ.setAttribute('r', '5');
            circ.setAttribute('class', 'wp-midpoint-handle');
            circ.setAttribute('data-seg-key', key);
            circ.setAttribute('data-seg-idx', String(i));
            svgEl.appendChild(circ);
        }

        // Draw.io style: ◇ Diamond waypoint handles at user-created waypoints
        if (hasWps) {
            wps.forEach(function(wp, idx) {
                var diam = mkSvg('polygon');
                var r = 7;
                var ptsArr = [
                    (wp.x) + ',' + (wp.y - r),
                    (wp.x + r) + ',' + (wp.y),
                    (wp.x) + ',' + (wp.y + r),
                    (wp.x - r) + ',' + (wp.y)
                ];
                diam.setAttribute('points', ptsArr.join(' '));
                diam.setAttribute('class', 'wp-handle');
                diam.setAttribute('data-wp-key', key);
                diam.setAttribute('data-wp-idx', String(idx));
                svgEl.appendChild(diam);
            });
        }
    }

    // ===== PANEL MODE SWITCHER =====
    function setPanelMode(mode) { // 'idle' | 'kotak' | 'garis'
        document.getElementById('panel-section-idle').classList.toggle('active', mode === 'idle');
        document.getElementById('panel-section-kotak').classList.toggle('active', mode === 'kotak');
        document.getElementById('panel-section-garis').classList.toggle('active', mode === 'garis');
    }

    // ===== SELECTION =====
    function selectNode(id, addToSelection) {
        if (!addToSelection) selectedNodeIds.clear();
        selectedNodeId = id;
        selectedNodeIds.add(id);
        refreshNodeClasses();
        updatePanel(id);
        setPanelMode('kotak');
        if (selectedNodeIds.size > 1) updateMultiSelectPanel();
        // Clear connector selection
        if (selectedConnectorKey) {
            selectedConnectorKey = null;
            document.getElementById('connector-color-picker-inline').classList.remove('show');
        }
        renderConnectors();
    }

    function toggleNodeInSelection(id) {
        if (selectedNodeIds.has(id)) {
            selectedNodeIds.delete(id);
            if (selectedNodeId === id) selectedNodeId = selectedNodeIds.size > 0 ? [...selectedNodeIds][0] : null;
        } else {
            selectedNodeIds.add(id);
            selectedNodeId = id;
        }
        refreshNodeClasses();
        if (selectedNodeId) {
            updatePanel(selectedNodeId);
            setPanelMode('kotak');
            if (selectedNodeIds.size > 1) updateMultiSelectPanel();
        } else { setPanelMode('idle'); }
    }

    function deselectAll() {
        selectedNodeId = null;
        selectedNodeIds.clear();
        refreshNodeClasses();
        document.getElementById('edit-nama-inline').value     = '';
        document.getElementById('edit-nip-inline').value      = '';
        document.getElementById('edit-pangkat-inline').value  = '';
        document.getElementById('edit-golongan-inline').value = '';
        document.getElementById('edit-jabatan-inline').value  = '';
        document.getElementById('edit-parent-inline').value   = '';
        document.getElementById('edit-x-inline').textContent  = '0';
        document.getElementById('edit-y-inline').textContent  = '0';
        colorSwatches.forEach(s => s.classList.remove('active'));
        colorSwatches[0].classList.add('active');
        setPanelMode('idle');
    }

    function refreshNodeClasses() {
        stage.querySelectorAll('.builder-node').forEach(function(el) {
            const id = parseInt(el.dataset.id);
            el.classList.toggle('selected',       id === selectedNodeId);
            el.classList.toggle('multi-selected', selectedNodeIds.has(id) && selectedNodeIds.size > 1);
        });
    }

    let pendingImageFile = null;
    let removeImageFlag = false;

    function populateParentDropdown(excludeNodeId, selectedParentId) {
        const select = document.getElementById('edit-parent-inline');
        if (!select) return;
        select.innerHTML = '<option value="">-- Root (Tanpa Atasan) --</option>';
        nodes.forEach(function(n) {
            if (n.id === excludeNodeId) return; // Tidak boleh atasan diri sendiri
            if (excludeNodeId && wouldCreateCycle(excludeNodeId, n.id)) return; // Tidak boleh turunan sendiri
            const opt = document.createElement('option');
            opt.value = n.id;
            const label = n.jabatan + (n.nama && n.nama !== '-' ? ' - ' + n.nama : '');
            opt.textContent = label;
            if (selectedParentId !== undefined && selectedParentId !== null && String(n.id) === String(selectedParentId)) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });
        if (selectedParentId) {
            select.value = String(selectedParentId);
        } else {
            select.value = '';
        }
    }

    function updatePanel(id) {
        const node = getNodeById(id); if (!node) return;
        document.getElementById('panel-multi-select-info').style.display = 'none';
        document.getElementById('panel-single-fields').style.display = '';
        document.getElementById('edit-nama-inline').value     = (node.nama && node.nama !== '-')  ? node.nama : '';
        document.getElementById('edit-nip-inline').value      = (node.nip  && node.nip  !== '-')  ? node.nip  : '';
        document.getElementById('edit-pangkat-inline').value  = (node.pangkat  && node.pangkat  !== '-') ? node.pangkat : '';
        document.getElementById('edit-golongan-inline').value = (node.golongan && node.golongan !== '-') ? node.golongan : '';
        document.getElementById('edit-jabatan-inline').value  = node.jabatan || '';
        populateParentDropdown(node.id, node.parent_id);
        document.getElementById('edit-x-inline').textContent  = node.x || 0;
        document.getElementById('edit-y-inline').textContent  = node.y || 0;
        colorSwatches.forEach(s => s.classList.toggle('active', s.dataset.color === (node.color || 'blue')));

        // Photo preview reset & update
        pendingImageFile = null;
        removeImageFlag = false;
        const fileInput = document.getElementById('edit-image-inline');
        if (fileInput) fileInput.value = '';

        const photoImg = document.getElementById('panel-photo-img');
        const photoPlaceholder = document.getElementById('panel-photo-placeholder');
        const btnRemove = document.getElementById('btn-remove-photo');
        if (node.foto_profile && String(node.foto_profile).trim() !== '') {
            if (photoImg) {
                photoImg.src = '{{ asset('images/struktur-organisasi') }}/' + encodeURIComponent(node.foto_profile);
                photoImg.style.display = 'block';
            }
            if (photoPlaceholder) photoPlaceholder.style.display = 'none';
            if (btnRemove) btnRemove.style.display = 'inline-flex';
        } else {
            if (photoImg) {
                photoImg.src = '';
                photoImg.style.display = 'none';
            }
            if (photoPlaceholder) photoPlaceholder.style.display = 'flex';
            if (btnRemove) btnRemove.style.display = 'none';
        }
    }

    function updateMultiSelectPanel() {
        panel.classList.add('open');
        document.getElementById('panel-multi-select-info').style.display = 'flex';
        document.getElementById('panel-single-fields').style.display = 'none';
        document.getElementById('panel-multi-count').textContent = selectedNodeIds.size;
    }

    colorSwatches.forEach(function(sw) {
        sw.addEventListener('click', function() {
            colorSwatches.forEach(s => s.classList.remove('active'));
            sw.classList.add('active');
            if (selectedNodeId) {
                const n = getNodeById(selectedNodeId);
                if (n) {
                    n.color = sw.dataset.color;
                    renderNodes();
                    if (n.id > 0) {
                        saveSingleNode(n).catch(function() {});
                    }
                }
            }
        });
    });

    // ===== DRAG =====
    let dragHasMoved = false;
    let dragStartClientX = 0, dragStartClientY = 0;

    function startDrag(e, id) {
        if (e.button !== 0) return;
        const el = getNodeEl(id); if (!el) return;
        dragNodeId = id;
        dragHasMoved = false;
        isDragging = false;
        dragStartClientX = e.clientX;
        dragStartClientY = e.clientY;
        const r = el.getBoundingClientRect();
        dragOffsetX = e.clientX - r.left;
        dragOffsetY = e.clientY - r.top;
        // Snapshot positions of ALL selected nodes + dragged node
        dragStartPositions = {};
        var dragSet = new Set(selectedNodeIds);
        dragSet.add(id);
        dragSet.forEach(function(nid) {
            const n = getNodeById(nid);
            if (n) dragStartPositions[nid] = { x: n.x || 0, y: n.y || 0 };
        });
        e.preventDefault();
    }

    // ===== CONNECT =====
    let connectFromPort = 'b';
    function startConnect(e, fromId, fromPort) {
        isConnecting = true; connectFromId = fromId; connectFromPort = fromPort || 'b';
        const fromEl = getNodeEl(fromId);
        if (!fromEl) { cleanupConnect(); return; }
        const r = fromEl.getBoundingClientRect(), br = body.getBoundingClientRect();
        const x1 = (r.left - br.left + r.width / 2 - panX) / zoomLevel;
        const y1 = (r.top  - br.top  + r.height / 2 - panY) / zoomLevel;
        // Draw.io style: snap temp line to nearest port on source
        var fromNode = getNodeById(fromId);
        var fromPorts = fromNode ? getNodePorts(fromNode) : null;
        var sx = x1, sy = y1;
        if (fromPorts && fromPorts[fromPort]) {
            sx = fromPorts[fromPort].x;
            sy = fromPorts[fromPort].y;
        }
        tempLine = mkSvg('line');
        tempLine.setAttribute('x1', sx); tempLine.setAttribute('y1', sy);
        tempLine.setAttribute('x2', sx); tempLine.setAttribute('y2', sy);
        tempLine.classList.add('connector-dragging');
        svgEl.appendChild(tempLine);
        e.preventDefault();
    }
    function cleanupConnect() { if (tempLine) { tempLine.remove(); tempLine = null; } isConnecting = false; connectFromId = null; }
    function findNodeAt(cx, cy) {
        const els = document.elementsFromPoint(cx, cy);
        for (var i = 0; i < els.length; i++) {
            const n = els[i].closest ? els[i].closest('.builder-node') : null;
            if (n) return getNodeById(parseInt(n.dataset.id));
        }
        return null;
    }
    function wouldCreateCycle(childId, newParentId) {
        let cur = newParentId; const v = new Set();
        while (cur) { if (cur === childId) return true; if (v.has(cur)) return true; v.add(cur); const p = getNodeById(cur); cur = p ? p.parent_id : null; }
        return false;
    }



    // ===== RUBBER-BAND =====
    function startRubberBand(e) {
        const bc = bodyCoords(e.clientX, e.clientY);
        isRubberBanding = true;
        rbBodyStartX = bc.x; rbBodyStartY = bc.y;
        rubberBandEl.style.cssText = 'left:' + bc.x + 'px;top:' + bc.y + 'px;width:0;height:0;';
        rubberBandEl.classList.add('show');
    }
    function updateRubberBand(e) {
        const bc = bodyCoords(e.clientX, e.clientY);
        rubberBandEl.style.left   = Math.min(rbBodyStartX, bc.x) + 'px';
        rubberBandEl.style.top    = Math.min(rbBodyStartY, bc.y) + 'px';
        rubberBandEl.style.width  = Math.abs(bc.x - rbBodyStartX) + 'px';
        rubberBandEl.style.height = Math.abs(bc.y - rbBodyStartY) + 'px';
    }
    function endRubberBand() {
        rubberBandEl.classList.remove('show');
        isRubberBanding = false;
        const rbR = rubberBandEl.getBoundingClientRect();
        if (rbR.width < 5 && rbR.height < 5) { deselectAll(); panel.classList.remove('open'); return; }
        const br = body.getBoundingClientRect();
        // Convert rubber-band rect to stage coords
        const sl = (rbR.left - br.left - panX) / zoomLevel;
        const st = (rbR.top  - br.top  - panY) / zoomLevel;
        const sr = sl + rbR.width  / zoomLevel;
        const sb = st + rbR.height / zoomLevel;
        const inRect = nodes.filter(function(n) {
            const nx = n.x || 0, ny = n.y || 0;
            return nx < sr && (nx + 288) > sl && ny < sb && (ny + 96) > st;
        });
        if (inRect.length > 0) {
            selectedNodeIds.clear();
            inRect.forEach(n => selectedNodeIds.add(n.id));
            selectedNodeId = inRect[0].id;
            refreshNodeClasses();
            if (inRect.length === 1) { updatePanel(inRect[0].id); panel.classList.add('open'); }
            else updateMultiSelectPanel();
        } else { deselectAll(); panel.classList.remove('open'); }
        renderConnectors();
    }

    // ===== MOUSE EVENTS =====
    body.addEventListener('mousedown', function(e) {
        // Middle mouse: pan
        if (e.button === 1) {
            e.preventDefault();
            isPanning = true; panStartX = e.clientX; panStartY = e.clientY;
            panStartPanX = panX; panStartPanY = panY;
            body.style.cursor = 'grabbing'; return;
        }
        // Left mouse on empty canvas or endpoint handle
        // Jangan nutup popup saat klik di context menu, toolbar, atau modal
        if (e.button === 0 && !e.target.closest('.builder-node') && !e.target.closest('.endpoint-handle-div')
            && !e.target.closest('#context-menu-inline') && !e.target.closest('.builder-panel-inline')
            && !e.target.closest('#wp-modal-inline') && !e.target.closest('#connector-color-picker-inline')) {
            if (selectedConnectorKey) {
                selectedConnectorKey = null;
                document.getElementById('connector-color-picker-inline').classList.remove('show');
                setPanelMode('idle');
                renderConnectors();
            }
            if (toolMode === 'select') {
                if (!e.shiftKey) deselectAll();
                startRubberBand(e);
            }
        }
    });

    // Draw.io style: endpoint handle mousedown on stage (HTML divs, not SVG)
    stage.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;
        var epDiv = e.target.closest('.endpoint-handle-div');
        if (!epDiv) return;
        e.stopPropagation(); e.preventDefault();
        isDraggingEndpoint = true;
        draggingEndpointKey = epDiv.dataset.epKey;
        draggingEndpointSide = epDiv.dataset.epSide;
        endpointDragStartClientX = e.clientX;
        endpointDragStartClientY = e.clientY;
        endpointHoverPort = null;
        // Get the fixed end (the opposite side of the connector) from actual anchored port geometry
        var epParts = draggingEndpointKey.split('-');
        if (epParts.length === 2) {
            var pNode = getNodeById(parseInt(epParts[0])), cNode = getNodeById(parseInt(epParts[1]));
            if (pNode && cNode) {
                var pp = getNodePorts(pNode), cp = getNodePorts(cNode);
                var uPorts = connectorPorts.get(draggingEndpointKey);
                var fd = (uPorts && uPorts.fromPort) || getBestPorts(pNode, cNode).fd;
                var td = (uPorts && uPorts.toPort) || getBestPorts(pNode, cNode).td;
                if (draggingEndpointSide === 'from') {
                    // Moving parent end -> fixed end is at child's toPort
                    endpointDragOrigFixedX = cp[td].x;
                    endpointDragOrigFixedY = cp[td].y;
                } else {
                    // Moving child end -> fixed end is at parent's fromPort
                    endpointDragOrigFixedX = pp[fd].x;
                    endpointDragOrigFixedY = pp[fd].y;
                }
            } else {
                var fixedId = draggingEndpointSide === 'from' ? parseInt(epParts[1]) : parseInt(epParts[0]);
                var fixedNode = getNodeById(fixedId);
                if (fixedNode) {
                    var fixedRect = getNodeRect(fixedNode);
                    endpointDragOrigFixedX = fixedRect.x + fixedRect.w / 2;
                    endpointDragOrigFixedY = fixedRect.y + fixedRect.h / 2;
                }
            }
        }
        // Create temp line for drag preview
        endpointTempLine = mkSvg('line');
        endpointTempLine.setAttribute('x1', endpointDragOrigFixedX);
        endpointTempLine.setAttribute('y1', endpointDragOrigFixedY);
        endpointTempLine.setAttribute('x2', endpointDragOrigFixedX);
        endpointTempLine.setAttribute('y2', endpointDragOrigFixedY);
        endpointTempLine.setAttribute('class', 'endpoint-temp-line');
        svgEl.appendChild(endpointTempLine);

        // Ghost original connector while dragging endpoint
        renderConnectors();
    });

    svgEl.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;

        // ○ Waypoint handle
        if (e.target.hasAttribute('data-wp-key') && e.target.hasAttribute('data-wp-idx')) {
            e.stopPropagation(); e.preventDefault();
            isDraggingWaypoint = true;
            draggingWpKey = e.target.getAttribute('data-wp-key');
            draggingWpIdx = parseInt(e.target.getAttribute('data-wp-idx'));
            return;
        }

        // □ Segment midpoint handle
        if (e.target.hasAttribute('data-seg-key') && e.target.hasAttribute('data-seg-idx')) {
            e.stopPropagation(); e.preventDefault();
            isDraggingSegment = true;
            draggingSegKey = e.target.getAttribute('data-seg-key');
            const si = e.target.getAttribute('data-seg-idx');
            draggingSegInsertAt = si === 'default-mid' ? -1 : parseInt(si);
            segDragHasMoved = false;
            segDragStartedWp = false;
            segDragClientStart = { x: e.clientX, y: e.clientY };
            return;
        }

        // Connector hit area
        if (e.target.hasAttribute('data-connector-hit')) {
            e.stopPropagation();
            const key = e.target.getAttribute('data-connector-hit');
            selectedConnectorKey = key;
            // Clear node selection
            selectedNodeId = null;
            selectedNodeIds.clear();
            refreshNodeClasses();
            renderConnectors();
            showConnectorPanel(key);
            return;
        }
        // SVG background → let bubble to body (starts rubber-band)
    });

    document.addEventListener('mousemove', function(e) {
        // Node drag (single or multi) — with draw.io style alignment snap
        if (dragNodeId !== null) {
            if (!isDragging) {
                var dist = Math.hypot(e.clientX - dragStartClientX, e.clientY - dragStartClientY);
                if (dist < 4) return; // Must move at least 4px to be considered a drag, not just a click!
                isDragging = true;
                dragHasMoved = true;
                const el = getNodeEl(dragNodeId); if (el) el.classList.add('dragging');
            }
            const el = getNodeEl(dragNodeId); if (!el) return;
            const br = body.getBoundingClientRect();
            var rawX = (e.clientX - panX - br.left - dragOffsetX) / zoomLevel;
            var rawY = (e.clientY - panY - br.top  - dragOffsetY) / zoomLevel;

            // ===== ALIGNMENT SNAP: check edges and centers against other nodes =====
            var draggedNode = getNodeById(dragNodeId);
            alignSnapX = null; alignSnapY = null; alignGuides = [];
            var bestDiffX = ALIGN_SNAP, bestDiffY = ALIGN_SNAP;

            if (draggedNode) {
                var dragSet = Object.keys(dragStartPositions).map(Number);
                var dRect = getNodeRect(draggedNode);
                var dL = rawX, dR = rawX + dRect.w, dC = rawX + dRect.w / 2;
                var dT = rawY, dB = rawY + dRect.h, dM = rawY + dRect.h / 2;

                nodes.forEach(function(other) {
                    if (dragSet.includes(other.id) || other.x === undefined || other.y === undefined) return;
                    var oRect = getNodeRect(other);
                    var oL = other.x, oR = other.x + oRect.w, oC = other.x + oRect.w / 2;
                    var oT = other.y, oB = other.y + oRect.h, oM = other.y + oRect.h / 2;

                    // Horizontal alignments: Center-Center (highest priority), Left-Left, Right-Right
                    var hCands = [
                        { dPos: dC, oPos: oC }, // Center
                        { dPos: dL, oPos: oL }, // Left-to-Left
                        { dPos: dR, oPos: oR }, // Right-to-Right
                        { dPos: dL, oPos: oR }, // Left-to-Right
                        { dPos: dR, oPos: oL }  // Right-to-Left
                    ];
                    hCands.forEach(function(hc) {
                        var diff = hc.oPos - hc.dPos;
                        var absDiff = Math.abs(diff);
                        if (absDiff < bestDiffX) {
                            bestDiffX = absDiff;
                            alignSnapX = diff;
                            var gStart = Math.min(dT, oT) - 30;
                            var gEnd   = Math.max(dB, oB) + 30;
                            alignGuides = alignGuides.filter(function(g) { return g.orient !== 'v'; });
                            alignGuides.push({ orient: 'v', pos: hc.oPos, start: gStart, end: gEnd });
                        }
                    });

                    // Vertical alignments: Middle-Middle, Top-Top, Bottom-Bottom
                    var vCands = [
                        { dPos: dM, oPos: oM }, // Middle
                        { dPos: dT, oPos: oT }, // Top-to-Top
                        { dPos: dB, oPos: oB }, // Bottom-to-Bottom
                        { dPos: dT, oPos: oB }, // Top-to-Bottom
                        { dPos: dB, oPos: oT }  // Bottom-to-Top
                    ];
                    vCands.forEach(function(vc) {
                        var diff = vc.oPos - vc.dPos;
                        var absDiff = Math.abs(diff);
                        if (absDiff < bestDiffY) {
                            bestDiffY = absDiff;
                            alignSnapY = diff;
                            var gStart = Math.min(dL, oL) - 30;
                            var gEnd   = Math.max(dR, oR) + 30;
                            alignGuides = alignGuides.filter(function(g) { return g.orient !== 'h'; });
                            alignGuides.push({ orient: 'h', pos: vc.oPos, start: gStart, end: gEnd });
                        }
                    });
                });
            }

            // Magnetic snap has PRIORITY! When magnetized, lock directly onto alignment coordinate.
            // When free of magnetic snap, fallback to granular grid snap.
            var alignedX = alignSnapX !== null 
                ? Math.round(Math.max(0, Math.min(3000, rawX + alignSnapX)))
                : snapV(Math.round(Math.max(0, Math.min(3000, rawX))));

            var alignedY = alignSnapY !== null 
                ? Math.round(Math.max(0, Math.min(2500, rawY + alignSnapY)))
                : snapV(Math.round(Math.max(0, Math.min(2500, rawY))));

            const sp = dragStartPositions[dragNodeId];
            if (sp) {
                const dx = alignedX - sp.x, dy = alignedY - sp.y;
                Object.keys(dragStartPositions).forEach(function(nidStr) {
                    const nid = parseInt(nidStr);
                    const n = getNodeById(nid); if (!n) return;
                    const ns = dragStartPositions[nid];
                    n.x = Math.max(0, ns.x + dx); n.y = Math.max(0, ns.y + dy);
                    const nel = getNodeEl(nid);
                    if (nel) { nel.style.left = n.x + 'px'; nel.style.top = n.y + 'px'; }
                });
                const pn = getNodeById(selectedNodeId);
                if (pn) {
                    document.getElementById('edit-x-inline').textContent = pn.x;
                    document.getElementById('edit-y-inline').textContent = pn.y;
                }
            }
            renderConnectors();
            renderAlignGuides();
            return;
        }

        // Pan
        if (isPanning) {
            panX = panStartPanX + (e.clientX - panStartX);
            panY = panStartPanY + (e.clientY - panStartY);
            applyTransform(); return;
        }

        // Connecting (temp line) — with magnetic snap ke port + indikator HIJAU (draw.io style)
        if (isConnecting && connectFromId !== null && tempLine) {
            var s = clientToStage(e.clientX, e.clientY);
            // Magnetic snap: find nearest port (radius lebih besar)
            var snapDist = 60;
            var snapPort = null;
            nodes.forEach(function(n) {
                if (n.id === connectFromId) return; // Skip source node
                var ports = getNodePorts(n);
                Object.keys(ports).forEach(function(pName) {
                    var p = ports[pName];
                    var dist = Math.hypot(p.x - s.x, p.y - s.y);
                    if (dist < snapDist) { snapDist = dist; snapPort = { x: p.x, y: p.y, port: pName, nodeId: n.id }; }
                });
            });
            if (snapPort) {
                tempLine.setAttribute('x2', snapPort.x);
                tempLine.setAttribute('y2', snapPort.y);
                // Pakai inline style biar ga ditimpa CSS class .connector-dragging
                tempLine.style.stroke = '#22c55e';
                tempLine.style.strokeWidth = '3.5';
                tempLine.style.filter = 'drop-shadow(0 0 6px rgba(34,197,94,0.5))';
            } else {
                tempLine.setAttribute('x2', s.x);
                tempLine.setAttribute('y2', s.y);
                // Reset ke CSS class default
                tempLine.style.stroke = '';
                tempLine.style.strokeWidth = '';
                tempLine.style.filter = '';
            }
            return;
        }

        // Rubber-band
        if (isRubberBanding) { updateRubberBand(e); return; }

        // ○ Waypoint drag — with smart magnetic alignment guides (draw.io style)
        if (isDraggingWaypoint && draggingWpKey) {
            const wps = connectorWaypoints.get(draggingWpKey);
            if (wps && wps[draggingWpIdx] !== undefined) {
                const s = clientToStage(e.clientX, e.clientY);
                showCoordTooltip(e.clientX, e.clientY, s.x, s.y, draggingWpKey);
                // Compute magnetic snap directly from stage cursor position
                var wpSnap = computeWaypointSnap(s.x, s.y, draggingWpKey);
                var alignedX = wpSnap.snapX !== null 
                    ? Math.round(s.x + wpSnap.snapX) 
                    : snapV(Math.round(s.x));
                var alignedY = wpSnap.snapY !== null 
                    ? Math.round(s.y + wpSnap.snapY) 
                    : snapV(Math.round(s.y));
                // Clamp within canvas stage bounds (0-4000, 0-3000)
                alignedX = Math.max(0, Math.min(4000, alignedX));
                alignedY = Math.max(0, Math.min(3000, alignedY));
                wps[draggingWpIdx] = { x: alignedX, y: alignedY };
                wpAlignGuides = wpSnap.guides;
                renderConnectors();
                renderWaypointGuides();
            }
            return;
        }

        // Draw.io style: ◉ Endpoint drag — magnetic snap ke port terdekat dgn indikator HIJAU!
        if (isDraggingEndpoint && draggingEndpointKey && draggingEndpointSide) {
            var s = clientToStage(e.clientX, e.clientY);
            // Grid snap fallback (kelipatan SNAP)
            var gx = snapV(Math.round(s.x));
            var gy = snapV(Math.round(s.y));
            // Find nearest port on ANY node — scaled by zoom level so it feels consistent at any zoom!
            var closestPort = null;
            var screenSnapRadiusPx = 45; // 45 pixels on user's actual screen
            var snapDist = Math.max(70, screenSnapRadiusPx / zoomLevel);

            // First priority: check if cursor is pointing directly at any node!
            var hoveredNode = findNodeAt(e.clientX, e.clientY);
            if (hoveredNode) {
                var hPorts = getNodePorts(hoveredNode);
                var bestD = Infinity;
                Object.keys(hPorts).forEach(function(pName) {
                    var p = hPorts[pName];
                    var d = Math.hypot(p.x - s.x, p.y - s.y);
                    if (d < bestD) {
                        bestD = d;
                        closestPort = { nodeId: hoveredNode.id, portName: pName, x: p.x, y: p.y };
                    }
                });
            } else {
                nodes.forEach(function(n) {
                    var ports = getNodePorts(n);
                    Object.keys(ports).forEach(function(pName) {
                        var p = ports[pName];
                        var dist = Math.hypot(p.x - s.x, p.y - s.y);
                        if (dist < snapDist) {
                            snapDist = dist;
                            closestPort = { nodeId: n.id, portName: pName, x: p.x, y: p.y };
                        }
                    });
                });
            }

            var snapX = s.x, snapY = s.y;
            var isSnapped = false;
            if (closestPort) {
                snapX = closestPort.x;
                snapY = closestPort.y;
                isSnapped = true;
                endpointHoverPort = closestPort;
            } else {
                endpointHoverPort = null;
                snapX = gx;
                snapY = gy;
            }
            snapX = Math.max(0, Math.min(4000, snapX));
            snapY = Math.max(0, Math.min(3000, snapY));

            if (endpointTempLine) {
                endpointTempLine.setAttribute('x1', endpointDragOrigFixedX);
                endpointTempLine.setAttribute('y1', endpointDragOrigFixedY);
                endpointTempLine.setAttribute('x2', snapX);
                endpointTempLine.setAttribute('y2', snapY);
                if (isSnapped) {
                    endpointTempLine.classList.add('endpoint-temp-line-snapped');
                    endpointTempLine.setAttribute('stroke', '#22c55e');
                    endpointTempLine.setAttribute('stroke-width', '3.5');
                    endpointTempLine.style.filter = 'drop-shadow(0 0 8px rgba(34,197,94,0.6))';
                } else {
                    endpointTempLine.classList.remove('endpoint-temp-line-snapped');
                    endpointTempLine.setAttribute('stroke', '#3b82f6');
                    endpointTempLine.setAttribute('stroke-width', '2');
                    endpointTempLine.style.filter = '';
                }
            }
            // Update port highlight — hijau saat snapped!
            var oldHighlight = svgEl.querySelector('.port-highlight-oval');
            if (oldHighlight) oldHighlight.remove();
            if (isSnapped && closestPort) {
                var hl = mkSvg('ellipse');
                hl.setAttribute('cx', closestPort.x); hl.setAttribute('cy', closestPort.y);
                hl.setAttribute('rx', '20'); hl.setAttribute('ry', '20');
                hl.setAttribute('class', 'port-highlight-oval');
                hl.style.fill = 'rgba(34,197,94,0.25)';
                hl.style.stroke = '#22c55e';
                hl.style.strokeWidth = '2.5';
                hl.style.strokeDasharray = 'none';
                svgEl.appendChild(hl);
            }
            return;
        }

        // □ Segment drag — draw.io style midpoint waypoint insert
        if (isDraggingSegment && draggingSegKey) {
            const dist = Math.hypot(e.clientX - segDragClientStart.x, e.clientY - segDragClientStart.y);
            if (dist > 3) {
                segDragHasMoved = true;
                const s = clientToStage(e.clientX, e.clientY);
                showCoordTooltip(e.clientX, e.clientY, s.x, s.y, draggingSegKey);

                if (!connectorWaypoints.has(draggingSegKey)) connectorWaypoints.set(draggingSegKey, []);
                const wps = connectorWaypoints.get(draggingSegKey);
                const insertAt = draggingSegInsertAt < 0 ? 0 : draggingSegInsertAt;

                if (!segDragStartedWp) {
                    var newWp = { x: snapV(Math.round(s.x)), y: snapV(Math.round(s.y)) };
                    wps.splice(Math.max(0, Math.min(wps.length, insertAt)), 0, newWp);
                    segDragStartedWp = true;
                    draggingWpIdx = insertAt;
                    draggingWpKey = draggingSegKey;
                    isDraggingWaypoint = true;
                    isDraggingSegment  = false;
                }
                renderConnectors();
            }
            return;
        }
    });

    document.addEventListener('mouseup', function(e) {
        // Clear hold timer
        if (connectorHoldTimer) { clearTimeout(connectorHoldTimer); connectorHoldTimer = null; }
        holdConnectorKey = null;

        if (dragNodeId !== null) {
            const el = getNodeEl(dragNodeId); if (el) el.classList.remove('dragging');
            if (isDragging && dragHasMoved) {
                pushUndo();
                clearAlignGuides();
            }
            isDragging = false; dragNodeId = null; dragStartPositions = {}; dragHasMoved = false;
            return;
        }
        if (isPanning) { isPanning = false; body.style.cursor = 'default'; return; }
        if (isRubberBanding) { endRubberBand(); return; }
        if (isDraggingEndpoint && draggingEndpointKey && draggingEndpointSide) {
            // Remove temp line and highlight
            if (endpointTempLine) { endpointTempLine.remove(); endpointTempLine = null; }
            var epHighlight = svgEl.querySelector('.port-highlight-oval');
            if (epHighlight) epHighlight.remove();
            // Check if dropped on a valid port
            if (endpointHoverPort) {
                var epParts = draggingEndpointKey.split('-');
                if (epParts.length === 2) {
                    var oldParentId = parseInt(epParts[0]), childId = parseInt(epParts[1]);
                    var targetNodeId = endpointHoverPort.nodeId;
                    var targetPort = endpointHoverPort.portName;
                    var childNode = getNodeById(childId);
                    if (childNode) {
                        pushUndo();
                        if (draggingEndpointSide === 'from' && targetNodeId !== oldParentId) {
                            // Endpoint dragged to a DIFFERENT node → reconnect parent!
                            if (!wouldCreateCycle(childId, targetNodeId)) {
                                var oldKey = oldParentId + '-' + childId;
                                var oldCol = connectorColors.get(oldKey);
                                var oldSty = connectorStyles.get(oldKey);
                                var oldPorts = connectorPorts.get(oldKey);
                                connectorWaypoints.delete(oldKey);
                                connectorColors.delete(oldKey);
                                connectorStyles.delete(oldKey);
                                connectorPorts.delete(oldKey);

                                childNode.parent_id = targetNodeId;
                                var newKey = targetNodeId + '-' + childId;
                                // Reset waypoints for the new connection so it routes cleanly
                                connectorWaypoints.delete(newKey);
                                if (oldCol) connectorColors.set(newKey, oldCol);
                                if (oldSty) connectorStyles.set(newKey, Object.assign({}, oldSty));
                                var origToPort = (oldPorts && oldPorts.toPort) || 't';
                                connectorPorts.set(newKey, { fromPort: targetPort, toPort: origToPort });
                                selectedConnectorKey = newKey;
                                showToast('Atasan dipindahkan ke node lain', 'success');
                                renderNodes();
                            } else {
                                showToast('Sirkular! Tidak bisa memindahkan ke sini', 'error');
                                renderConnectors();
                            }
                        } else if (draggingEndpointSide === 'to' && targetNodeId !== childId) {
                            // Endpoint dragged to a DIFFERENT node → reconnect child!
                            if (!wouldCreateCycle(targetNodeId, oldParentId)) {
                                var oldKey = oldParentId + '-' + childId;
                                var oldCol = connectorColors.get(oldKey);
                                var oldSty = connectorStyles.get(oldKey);
                                var oldPorts = connectorPorts.get(oldKey);
                                connectorWaypoints.delete(oldKey);
                                connectorColors.delete(oldKey);
                                connectorStyles.delete(oldKey);
                                connectorPorts.delete(oldKey);

                                var targetChild = getNodeById(targetNodeId);
                                if (targetChild) {
                                    childNode.parent_id = null;
                                    targetChild.parent_id = oldParentId;
                                    var newKey = oldParentId + '-' + targetNodeId;
                                    // Reset waypoints for the new connection so it routes cleanly
                                    connectorWaypoints.delete(newKey);
                                    if (oldCol) connectorColors.set(newKey, oldCol);
                                    if (oldSty) connectorStyles.set(newKey, Object.assign({}, oldSty));
                                    var origFromPort = (oldPorts && oldPorts.fromPort) || 'b';
                                    connectorPorts.set(newKey, { fromPort: origFromPort, toPort: targetPort });
                                    selectedConnectorKey = newKey;
                                    showToast('Bawahan dipindahkan ke node lain', 'success');
                                    renderNodes();
                                }
                            } else {
                                showToast('Sirkular! Tidak bisa memindahkan ke sini', 'error');
                                renderConnectors();
                            }
                        } else {
                            // Same node - just change port
                            if (draggingEndpointSide === 'from') {
                                var curPorts = connectorPorts.get(draggingEndpointKey) || {};
                                connectorPorts.set(draggingEndpointKey, {
                                    fromPort: targetPort,
                                    toPort: curPorts.toPort || 't'
                                });
                            } else {
                                var curPorts = connectorPorts.get(draggingEndpointKey) || {};
                                connectorPorts.set(draggingEndpointKey, {
                                    fromPort: curPorts.fromPort || 'b',
                                    toPort: targetPort
                                });
                            }
                            showToast('Port ' + (draggingEndpointSide === 'from' ? 'asal' : 'tujuan') + ' diubah ke ' + targetPort.toUpperCase(), 'success');
                            renderConnectors();
                        }
                        persistConnectorData();
                        // Update toolbar dropdowns
                        if (selectedConnectorKey) showConnectorToolbar(selectedConnectorKey);
                    }
                }
            } else {
                // Dropped outside any port → just render connectors normally (no change)
                renderConnectors();
            }
            isDraggingEndpoint = false; draggingEndpointKey = null; draggingEndpointSide = null;
            endpointHoverPort = null;
            return;
        }

        if (isDraggingWaypoint || isDraggingSegment) {
            isDraggingWaypoint = false; draggingWpKey = null; draggingWpIdx = null;
            isDraggingSegment = false; draggingSegKey = null; draggingSegInsertAt = null;
            segDragStartedWp = false;
            clearWaypointGuides();
            hideCoordTooltip();
            pushUndo(); persistConnectorData(); return;
        }
        if (isConnecting && connectFromId !== null) {
            const target = findNodeAt(e.clientX, e.clientY);
            let targetPort = 't';
            const hitHandle = document.elementFromPoint(e.clientX, e.clientY);
            if (hitHandle && hitHandle.classList.contains('node-port-arrow')) {
                if (hitHandle.classList.contains('node-port-arrow-t')) targetPort = 't';
                if (hitHandle.classList.contains('node-port-arrow-b')) targetPort = 'b';
                if (hitHandle.classList.contains('node-port-arrow-l')) targetPort = 'l';
                if (hitHandle.classList.contains('node-port-arrow-r')) targetPort = 'r';
            }
            if (target && target.id !== connectFromId) {
                const cn = getNodeById(connectFromId);
                if (cn) {
                    pushUndo();
                    // Menentukan arah relasi:
                    // Jika ditarik dari port atas ('t') cn -> cn adalah bawahan, target adalah atasan
                    // Jika ditarik dari port bawah ('b') / sisi lain -> cn adalah atasan, target adalah bawahan
                    let parentNode, childNode, pPort, cPort;
                    if (connectFromPort === 't') {
                        parentNode = target;
                        childNode = cn;
                        pPort = targetPort || 'b';
                        cPort = connectFromPort;
                    } else {
                        parentNode = cn;
                        childNode = target;
                        pPort = connectFromPort || 'b';
                        cPort = targetPort || 't';
                    }

                    if (!wouldCreateCycle(childNode.id, parentNode.id)) {
                        childNode.parent_id = parentNode.id;
                        const key = parentNode.id + '-' + childNode.id;
                        connectorPorts.set(key, { fromPort: pPort, toPort: cPort });
                        renderConnectors();
                        if (selectedNodeId === childNode.id) {
                            populateParentDropdown(childNode.id, childNode.parent_id);
                        }
                        showToast('Berhasil menghubungkan: ' + parentNode.jabatan + ' → ' + childNode.jabatan, 'success');
                        persistConnectorData();
                    } else {
                        showToast('Sirkular! Tidak bisa menghubungkan hirarki melingkar', 'error');
                    }
                }
            } else if (!target) {
                const r = body.getBoundingClientRect();
                addNode((e.clientX - panX - r.left) / zoomLevel - 100, (e.clientY - panY - r.top) / zoomLevel - 30, connectFromId);
            }
            cleanupConnect();
            return;
        }
    });

    // Endpoint drag cleanup — handle mouse leaving window
    document.addEventListener('mouseleave', function() {
        if (isDraggingEndpoint) {
            if (endpointTempLine) { endpointTempLine.remove(); endpointTempLine = null; }
            var epHighlight = svgEl.querySelector('.port-highlight-oval');
            if (epHighlight) epHighlight.remove();
            renderConnectors();
            isDraggingEndpoint = false; draggingEndpointKey = null; draggingEndpointSide = null;
            endpointHoverPort = null;
        }
    });
    // Also handle window blur (Alt+Tab, etc.)
    window.addEventListener('blur', function() {
        if (isDraggingEndpoint) {
            if (endpointTempLine) { endpointTempLine.remove(); endpointTempLine = null; }
            var epHighlight = svgEl.querySelector('.port-highlight-oval');
            if (epHighlight) epHighlight.remove();
            renderConnectors();
            isDraggingEndpoint = false; draggingEndpointKey = null; draggingEndpointSide = null;
            endpointHoverPort = null;
        }
    });

    document.addEventListener('mousedown', function(e) { if (e.button === 1) e.preventDefault(); });
    body.addEventListener('wheel', function(e) {
        e.preventDefault();
        setZoom(zoomLevel + (e.deltaY > 0 ? -0.06 : 0.06), e.clientX, e.clientY);
    }, { passive: false });

    // ===== DRAW.IO STYLE: CONNECTOR HOVER GLOW =====
    svgEl.addEventListener('mouseover', function(e) {
        var hp = e.target.closest('[data-connector-hit]');
        if (!hp) {
            var glow = e.target.closest('[data-connector-glow]');
            if (glow) {
                var key = glow.getAttribute('data-connector-glow');
                if (key && key !== selectedConnectorKey) {
                    glow.setAttribute('stroke', 'rgba(59,130,246,0.12)');
                }
            }
            return;
        }
        // On hover, show a subtle highlight on the glow path
        var key = hp.getAttribute('data-connector-hit');
        var glow = svgEl.querySelector('[data-connector-glow="' + key + '"]');
        if (glow && key !== selectedConnectorKey) {
            glow.setAttribute('stroke', 'rgba(59,130,246,0.12)');
        }
    });

    svgEl.addEventListener('mouseout', function(e) {
        var hp = e.target.closest('[data-connector-hit]');
        if (!hp) {
            var glow = e.target.closest('[data-connector-glow]');
            if (glow) {
                var key = glow.getAttribute('data-connector-glow');
                if (key && key !== selectedConnectorKey) {
                    glow.setAttribute('stroke', 'transparent');
                }
            }
            return;
        }
        var key = hp.getAttribute('data-connector-hit');
        var glow = svgEl.querySelector('[data-connector-glow="' + key + '"]');
        if (glow && key !== selectedConnectorKey) {
            glow.setAttribute('stroke', 'transparent');
        }
    });

    // ===== CONNECTOR & WAYPOINT RIGHT-CLICK CONTEXT MENU =====
    svgEl.addEventListener('contextmenu', function(e) {
        // Prioritaskan: waypoint → connector → canvas
        var wpTarget = e.target.closest('[data-wp-key]');
        var connectorTarget = e.target.closest('[data-connector-hit]');

        if (wpTarget) {
            e.preventDefault(); e.stopPropagation();
            const key = wpTarget.getAttribute('data-wp-key');
            const idx = parseInt(wpTarget.getAttribute('data-wp-idx'));
            // Tampilkan context menu untuk waypoint
            hideAllCtxItems();
            document.querySelector('.ctx-waypoint-item').style.display = 'flex';
            contextMenu.dataset.wpKey = key;
            contextMenu.dataset.wpIdx = idx;
            contextMenu.style.left = e.clientX + 'px';
            contextMenu.style.top = e.clientY + 'px';
            contextMenu.classList.add('show');
            return;
        }

        if (connectorTarget) {
            e.preventDefault(); e.stopPropagation();
            const key = connectorTarget.getAttribute('data-connector-hit');
            // Tampilkan context menu untuk connector
            hideAllCtxItems();
            document.querySelectorAll('.ctx-connector-item').forEach(function(el) { el.style.display = 'flex'; });
            contextMenu.dataset.connectorKey = key;
            // Nonaktifkan Simplify jika tidak ada waypoint
            var hasWps = connectorWaypoints.has(key) && connectorWaypoints.get(key).length > 0;
            if (!hasWps) {
                document.querySelector('[data-action="clear-waypoints"]').style.display = 'none';
                document.querySelector('[data-action="simplify"]').style.display = 'none';
                document.querySelector('[data-action="edit-waypoints"]').style.display = 'none';
            }
            contextMenu.style.left = e.clientX + 'px';
            contextMenu.style.top = e.clientY + 'px';
            contextMenu.classList.add('show');
            return;
        }
    });

    function hideAllCtxItems() {
        document.querySelectorAll('.ctx-node-item, .ctx-connector-item, .ctx-waypoint-item').forEach(function(el) {
            el.style.display = 'none';
        });
        document.querySelectorAll('.ctx-node-sep').forEach(function(el) { el.style.display = 'none'; });
    }

    // ===== CONNECTOR DOUBLE-CLICK: ADD WAYPOINT =====
    svgEl.addEventListener('dblclick', function(e) {
        const hp = e.target.closest('[data-connector-hit]');
        if (!hp) return;
        const key = hp.getAttribute('data-connector-hit');
        const s = clientToStage(e.clientX, e.clientY);
        if (!connectorWaypoints.has(key)) connectorWaypoints.set(key, []);
        pushUndo();
        connectorWaypoints.get(key).push({ x: Math.round(s.x), y: Math.round(s.y) });
        renderConnectors(); showToast('Titik belok ditambahkan', 'success');
    });

    // ===== CONNECTOR PANEL (right panel - Edit Garis) =====
    function showConnectorPanel(key) {
        if (!key) { setPanelMode('idle'); return; }

        var st = getConnectorStyle(key);
        var curColor = connectorColors.get(key) || '';
        var parts = key.split('-');
        var pNode = getNodeById(parseInt(parts[0]));
        var cNode = getNodeById(parseInt(parts[1]));

        var ports = connectorPorts.get(key);
        var fromP = 'b', toP = 't';
        if (ports && ports.fromPort && ports.toPort) {
            fromP = ports.fromPort; toP = ports.toPort;
        } else if (pNode && cNode) {
            var autoP = getBestPorts(pNode, cNode);
            fromP = autoP.fd; toP = autoP.td;
        }

        document.getElementById('ct-from-port').value = fromP;
        document.getElementById('ct-to-port').value   = toP;

        // Highlight active style button
        document.querySelectorAll('#ct-style-group .ct-btn').forEach(function(b) {
            b.classList.toggle('active', b.dataset.style === (st.type || 'orthogonal'));
        });
        // Highlight active dash button
        document.querySelectorAll('#ct-dash-group .ct-btn').forEach(function(b) {
            b.classList.toggle('active', b.dataset.dash === (st.dash || 'solid'));
        });
        // Highlight active color swatch
        document.querySelectorAll('#ct-color-swatches .cg-swatch').forEach(function(s) {
            s.classList.toggle('active', s.dataset.color === curColor);
        });

        setPanelMode('garis');
    }
    // alias for backward compat (endpoint drag etc)
    function showConnectorToolbar(key) { showConnectorPanel(key); }

    document.getElementById('ct-from-port').addEventListener('change', function() {
        if (!selectedConnectorKey) return;
        var cur = connectorPorts.get(selectedConnectorKey) || { fromPort: 'b', toPort: 't' };
        connectorPorts.set(selectedConnectorKey, { fromPort: this.value, toPort: cur.toPort });
        pushUndo(); renderConnectors(); showToast('Port asal diubah', 'success'); persistConnectorData();
    });

    document.getElementById('ct-to-port').addEventListener('change', function() {
        if (!selectedConnectorKey) return;
        var cur = connectorPorts.get(selectedConnectorKey) || { fromPort: 'b', toPort: 't' };
        connectorPorts.set(selectedConnectorKey, { fromPort: cur.fromPort, toPort: this.value });
        pushUndo(); renderConnectors(); showToast('Port tujuan diubah', 'success'); persistConnectorData();
    });

    // ===== Bind Connector Panel buttons (Edit Garis) =====
    document.querySelectorAll('#ct-style-group .ct-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!selectedConnectorKey) return;
            var cur = getConnectorStyle(selectedConnectorKey);
            connectorStyles.set(selectedConnectorKey, { type: btn.dataset.style, dash: cur.dash || 'solid' });
            pushUndo(); renderConnectors(); showConnectorPanel(selectedConnectorKey);
            showToast('Bentuk garis: ' + btn.title, 'success'); persistConnectorData();
        });
    });

    document.querySelectorAll('#ct-dash-group .ct-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!selectedConnectorKey) return;
            var cur = getConnectorStyle(selectedConnectorKey);
            connectorStyles.set(selectedConnectorKey, { type: cur.type || 'orthogonal', dash: btn.dataset.dash });
            pushUndo(); renderConnectors(); showConnectorPanel(selectedConnectorKey);
            showToast('Tipe garis diubah', 'success'); persistConnectorData();
        });
    });

    document.querySelectorAll('#ct-color-swatches .cg-swatch').forEach(function(sw) {
        sw.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!selectedConnectorKey) return;
            pushUndo(); connectorColors.set(selectedConnectorKey, sw.dataset.color);
            renderConnectors(); showConnectorPanel(selectedConnectorKey);
            showToast('Warna garis diubah', 'success'); persistConnectorData();
        });
    });

    document.getElementById('cg-reset-btn').addEventListener('click', function(e) {
        e.stopPropagation(); if (!selectedConnectorKey) return;
        pushUndo(); connectorColors.delete(selectedConnectorKey);
        renderConnectors(); showConnectorPanel(selectedConnectorKey);
        showToast('Warna garis di-reset', 'info'); persistConnectorData();
    });

    document.getElementById('ct-delete-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        if (!selectedConnectorKey) return;
        var parts = selectedConnectorKey.split('-');
        var childId = parseInt(parts[1]);
        var childNode = getNodeById(childId);
        if (childNode) {
            pushUndo();
            childNode.parent_id = null;
            connectorWaypoints.delete(selectedConnectorKey);
            connectorColors.delete(selectedConnectorKey);
            connectorStyles.delete(selectedConnectorKey);
            connectorPorts.delete(selectedConnectorKey);
            selectedConnectorKey = null;
            setPanelMode('idle');
            renderNodes(); showToast('Koneksi dihapus', 'info'); persistConnectorData();
        }
    });

    // ===== COLOR PICKER =====
    document.querySelectorAll('#connector-color-picker-inline .cp-swatch').forEach(function(sw) {
        sw.addEventListener('click', function(e) {
            e.stopPropagation(); if (!selectedConnectorKey) return;
            pushUndo(); connectorColors.set(selectedConnectorKey, sw.dataset.color);
            document.getElementById('connector-color-picker-inline').classList.remove('show');
            renderConnectors(); showToast('Warna garis diubah', 'success'); persistConnectorData();
        });
    });
    document.getElementById('cp-reset-inline').addEventListener('click', function(e) {
        e.stopPropagation(); if (!selectedConnectorKey) return;
        pushUndo(); connectorColors.delete(selectedConnectorKey);
        document.getElementById('connector-color-picker-inline').classList.remove('show');
        renderConnectors(); showToast('Warna garis di-reset', 'info'); persistConnectorData();
    });

    function persistConnectorData() {
        const d = {
            waypoints: Object.fromEntries(connectorWaypoints),
            colors: Object.fromEntries(connectorColors),
            styles: Object.fromEntries(connectorStyles),
            ports: Object.fromEntries(connectorPorts)
        };
        try { localStorage.setItem('struktur_connector_data', JSON.stringify(d)); } catch (ex) {}
    }

    // ===== TOOL MODE =====
    document.getElementById('tool-select-btn').addEventListener('click', function() {
        toolMode = 'select';
        document.getElementById('tool-select-btn').classList.add('active');
        document.getElementById('tool-connect-btn').classList.remove('active');
        body.style.cursor = 'default';
    });
    document.getElementById('tool-connect-btn').addEventListener('click', function() {
        toolMode = 'connect';
        document.getElementById('tool-connect-btn').classList.add('active');
        document.getElementById('tool-select-btn').classList.remove('active');
        body.style.cursor = 'crosshair';
    });

    // ===== CRUD =====
    function addNode(x, y, parentId) {
        pushUndo(); const id = tempIdCounter--;
        const node = { id, jabatan: 'Jabatan Baru', nama: '-', nip: '-', golongan: '-', pangkat: '-', foto_profile: null, parent_id: parentId || null, x: snapV(x), y: snapV(y), color: 'blue' };
        nodes.push(node); renderNodes(); selectNode(id, false); showToast('Kotak baru ditambahkan', 'success');
        saveSingleNode(node).catch(function() {}); return node;
    }
    function duplicateNode(id) {
        const src = getNodeById(id); if (!src) return;
        const newId = tempIdCounter--;
        const node = Object.assign({}, src, { id: newId, x: (src.x || 0) + 40, y: (src.y || 0) + 40, parent_id: null, nama: '-', nip: '-', foto_profile: null });
        pushUndo(); nodes.push(node); renderNodes(); selectNode(newId, false); showToast('Kotak diduplikasi', 'success');
        saveSingleNode(node).catch(function() {});
    }
    function deleteNode(id) {
        if (!confirm('Hapus kotak ini?')) return;
        pushUndo();
        nodes.forEach(function(n) { if (n.parent_id === id) n.parent_id = null; });
        nodes = nodes.filter(function(n) { return n.id !== id; });
        selectedNodeIds.delete(id);
        if (selectedNodeId === id) {
            selectedNodeId = selectedNodeIds.size > 0 ? [...selectedNodeIds][0] : null;
            if (!selectedNodeId) { deselectAll(); panel.classList.remove('open'); }
        }
        renderNodes();
        if (selectedNodeId) {
            const sn = getNodeById(selectedNodeId);
            if (sn) populateParentDropdown(sn.id, sn.parent_id);
        }
        if (id > 0) fetch('{{ url('/strukturors-builder/delete') }}/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } }).catch(function() {});
        showToast('Kotak berhasil dihapus', 'info');
    }
    function deleteSelectedNodes() {
        if (selectedNodeIds.size === 0) return;
        if (!confirm('Hapus ' + selectedNodeIds.size + ' kotak yang dipilih?')) return;
        pushUndo();
        const toDelete = [...selectedNodeIds];
        toDelete.forEach(function(id) {
            nodes.forEach(function(n) { if (n.parent_id === id) n.parent_id = null; });
            nodes = nodes.filter(function(n) { return n.id !== id; });
            if (id > 0) fetch('{{ url('/strukturors-builder/delete') }}/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } }).catch(function() {});
        });
        deselectAll(); panel.classList.remove('open');
        renderNodes(); showToast(toDelete.length + ' kotak dihapus', 'info');
    }

    function saveSingleNode(node, imageFile = null, removeImage = false) {
        const isNew = node.id < 0;
        const url = isNew ? '{{ route('strukturors.store-box') }}' : '{{ url('/strukturors-builder/update') }}/' + node.id;
        const parentId = isNew && node.parent_id < 0 ? null : node.parent_id;

        const formData = new FormData();
        if (!isNew) {
            formData.append('_method', 'PUT');
        }
        formData.append('jabatan', node.jabatan || '');
        formData.append('nama', node.nama || '-');
        if (node.nip && node.nip !== '-') {
            formData.append('nip', node.nip);
        }
        formData.append('golongan', node.golongan || '-');
        formData.append('pangkat', node.pangkat || '-');
        formData.append('x', node.x || 0);
        formData.append('y', node.y || 0);
        formData.append('parent_id', (parentId !== null && parentId !== undefined && parentId !== '' && parentId > 0) ? parentId : '');
        if (node.color) formData.append('color', node.color);

        if (imageFile) {
            formData.append('image', imageFile);
        }
        if (removeImage) {
            formData.append('remove_image', '1');
        }

        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        }).then(function(r) {
            if (!r.ok) return r.json().then(err => { throw new Error(err.message || 'Gagal menyimpan'); });
            return r.json();
        }).then(function(d) {
            if (d.node) {
                const idx = nodes.findIndex(function(n) { return n.id === node.id; });
                if (idx !== -1) {
                    const oid = node.id, origPid = nodes[idx].parent_id;
                    nodes[idx] = Object.assign({}, nodes[idx], d.node);
                    if (origPid < 0 && d.node.parent_id === null) nodes[idx].parent_id = origPid;

                    // Remap children and connector keys if temporary ID changed to database ID
                    if (oid !== d.node.id) {
                        nodes.forEach(function(n) {
                            if (n.parent_id === oid) n.parent_id = d.node.id;
                        });
                        function remapK(map) {
                            const entries = [...map.entries()];
                            entries.forEach(function([k, v]) {
                                const p = k.split('-');
                                if (parseInt(p[0]) === oid || parseInt(p[1]) === oid) {
                                    map.delete(k);
                                    const newK = (parseInt(p[0]) === oid ? d.node.id : p[0]) + '-' + (parseInt(p[1]) === oid ? d.node.id : p[1]);
                                    map.set(newK, v);
                                }
                            });
                        }
                        remapK(connectorWaypoints);
                        remapK(connectorColors);
                        remapK(connectorStyles);
                        remapK(connectorPorts);
                    }

                    if (selectedNodeId === oid && d.node.id !== oid) {
                        selectedNodeId = d.node.id;
                        selectedNodeIds.delete(oid);
                        selectedNodeIds.add(d.node.id);
                    }
                    renderNodes();
                    if (selectedNodeId === d.node.id) {
                        selectNode(d.node.id, false);
                        populateParentDropdown(d.node.id, nodes[idx].parent_id);
                    }
                    return { oldId: oid, newId: d.node.id };
                }
            }
            return null;
        });
    }

    function saveAllLayout() {
        if (nodes.length === 0) { showToast('Tidak ada data', 'info'); return; }
        const btn = document.getElementById('btn-save-layout-inline');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        const unsaved = nodes.filter(function(n) { return n.id < 0; });
        const idMap = {};
        const chain = unsaved.reduce(function(p, node) {
            return p.then(function() { return saveSingleNode(node); }).then(function(m) { if (m) idMap[m.oldId] = m.newId; });
        }, Promise.resolve());
        chain.then(function() {
            function remapKey(key) {
                const p = key.split('-');
                return (idMap[parseInt(p[0])] || p[0]) + '-' + (idMap[parseInt(p[1])] || p[1]);
            }
            const remWps = new Map(); for (const [k, v] of connectorWaypoints) remWps.set(remapKey(k), v);
            connectorWaypoints.clear(); for (const [k, v] of remWps) connectorWaypoints.set(k, v);
            const remCols = new Map(); for (const [k, v] of connectorColors) remCols.set(remapKey(k), v);
            connectorColors.clear(); for (const [k, v] of remCols) connectorColors.set(k, v);
            const remStys = new Map(); for (const [k, v] of connectorStyles) remStys.set(remapKey(k), v);
            connectorStyles.clear(); for (const [k, v] of remStys) connectorStyles.set(k, v);
            const remPrts = new Map(); for (const [k, v] of connectorPorts) remPrts.set(remapKey(k), v);
            connectorPorts.clear(); for (const [k, v] of remPrts) connectorPorts.set(k, v);
            const updatedNodes = nodes.map(function(n) {
                let pid = n.parent_id;
                if (pid && idMap[pid]) pid = idMap[pid];
                if (pid && pid <= 0) pid = null;
                return {
                    id: n.id,
                    x: n.x || 0,
                    y: n.y || 0,
                    parent_id: pid || null,
                    color: n.color || 'blue'
                };
            });
            const lsData = {
                waypoints: Object.fromEntries(connectorWaypoints),
                colors: Object.fromEntries(connectorColors),
                styles: Object.fromEntries(connectorStyles),
                ports: Object.fromEntries(connectorPorts)
            };
            try { localStorage.setItem('struktur_connector_data', JSON.stringify(lsData)); } catch (ex) {}
            return fetch('{{ route('strukturors.save-layout') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    nodes: updatedNodes,
                    waypoints: Object.fromEntries(connectorWaypoints),
                    connector_colors: Object.fromEntries(connectorColors),
                    connector_styles: Object.fromEntries(connectorStyles),
                    connector_ports: Object.fromEntries(connectorPorts)
                })
            });
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) showToast('✅ Tata letak berhasil disimpan!', 'success');
            else showToast('Gagal: ' + (d.message || ''), 'error');
        }).catch(function(err) { showToast('Gagal: ' + err.message, 'error'); })
        .finally(function() { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan'; });
    }

    // ===== UNDO =====
    function pushUndo() {
        undoStack.push({
            nodes: JSON.parse(JSON.stringify(nodes)),
            waypoints: new Map(Array.from(connectorWaypoints.entries()).map(function(e) { return [e[0], e[1].slice()]; })),
            colors:    new Map(Array.from(connectorColors.entries())),
            styles:    new Map(Array.from(connectorStyles.entries()).map(function(e) { return [e[0], Object.assign({}, e[1])]; })),
            ports:     new Map(Array.from(connectorPorts.entries()).map(function(e) { return [e[0], Object.assign({}, e[1])]; }))
        });
        if (undoStack.length > 30) undoStack.shift();
        document.getElementById('btn-undo-inline').disabled = false;
    }
    function undo() {
        if (undoStack.length === 0) return;
        const sn = undoStack.pop();
        nodes = sn.nodes;
        connectorWaypoints.clear(); for (const [k, v] of sn.waypoints) connectorWaypoints.set(k, v);
        connectorColors.clear();    for (const [k, v] of sn.colors)    connectorColors.set(k, v);
        connectorStyles.clear();    if (sn.styles) for (const [k, v] of sn.styles) connectorStyles.set(k, v);
        connectorPorts.clear();     if (sn.ports)  for (const [k, v] of sn.ports)  connectorPorts.set(k, v);
        if (undoStack.length === 0) document.getElementById('btn-undo-inline').disabled = true;
        if (selectedNodeId && !getNodeById(selectedNodeId)) { deselectAll(); panel.classList.remove('open'); }
        renderNodes();
        if (selectedNodeId) updatePanel(selectedNodeId);
        showToast('Undo berhasil', 'info');
    }

    // ===== CONTEXT MENU =====
    function showContextMenu(x, y, nodeId) {
        hideAllCtxItems();
        document.querySelectorAll('.ctx-node-item').forEach(function(el) { el.style.display = 'flex'; });
        document.querySelectorAll('.ctx-node-sep').forEach(function(el) { el.style.display = 'block'; });
        contextMenu.style.left = x + 'px'; contextMenu.style.top = y + 'px';
        contextMenu.classList.add('show'); contextMenu.dataset.nodeId = nodeId;
    }
    contextMenu.addEventListener('click', function(e) {
        const item = e.target.closest('.ctx-item'); if (!item) return;
        const action = item.dataset.action;
        contextMenu.classList.remove('show');

        // Connector actions
        if (action === 'clear-waypoints') {
            const key = contextMenu.dataset.connectorKey;
            if (key) { pushUndo(); connectorWaypoints.delete(key); renderConnectors(); showToast('Semua belokan dihapus', 'info'); persistConnectorData(); }
            return;
        }
        if (action === 'simplify') {
            const key = contextMenu.dataset.connectorKey;
            if (key) { pushUndo(); connectorWaypoints.delete(key); renderConnectors(); showToast('Garis disederhanakan', 'success'); persistConnectorData(); }
            return;
        }
        if (action === 'edit-waypoints') {
            const key = contextMenu.dataset.connectorKey;
            if (key) openWaypointEditor(key);
            return;
        }

        // Waypoint actions
        if (action === 'remove-wp') {
            const key = contextMenu.dataset.wpKey;
            const idx = parseInt(contextMenu.dataset.wpIdx);
            if (key && !isNaN(idx)) {
                pushUndo();
                const wps = connectorWaypoints.get(key);
                if (wps) { wps.splice(idx, 1); if (wps.length === 0) connectorWaypoints.delete(key); }
                renderConnectors(); showToast('Titik belok dihapus', 'info'); persistConnectorData();
            }
            return;
        }

        // Node actions
        const nodeId = parseInt(contextMenu.dataset.nodeId);
        if (!isNaN(nodeId)) {
            switch (action) {
                case 'add-child': { const n = getNodeById(nodeId); if (n) addNode((n.x || 0) + 20, (n.y || 0) + 120, nodeId); break; }
                case 'duplicate': duplicateNode(nodeId); break;
                case 'edit': selectNode(nodeId, false); document.getElementById('edit-jabatan-inline').focus(); break;
                case 'delete': deleteNode(nodeId); break;
            }
        }
    });
    document.addEventListener('click', function() {
        contextMenu.classList.remove('show');
        contextMenu.innerHTML = ctxMenuOriginalHtml;
        contextMenu.dataset.connectorKey = '';
        contextMenu.dataset.wpKey = '';
        contextMenu.dataset.wpIdx = '';
    });
    body.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.builder-node')) return;
        if (e.target.closest('#svg-connectors-inline')) return;
        e.preventDefault();
        const r = body.getBoundingClientRect();
        const x = (e.clientX - panX - r.left) / zoomLevel, y = (e.clientY - panY - r.top) / zoomLevel;
        contextMenu.innerHTML = '<div class="ctx-item" data-action="add-here"><i class="fas fa-plus-circle"></i> Tambah Node di Sini</div>';
        contextMenu.querySelector('[data-action="add-here"]').addEventListener('click', function(ev) {
            ev.stopPropagation(); addNode(x, y, null); contextMenu.classList.remove('show'); contextMenu.innerHTML = ctxMenuOriginalHtml;
        });
        contextMenu.style.left = e.clientX + 'px'; contextMenu.style.top = e.clientY + 'px';
        contextMenu.classList.add('show');
    });

    // ===== TOAST =====
    function showToast(msg, type) {
        type = type || 'info';
        const c = document.getElementById('builder-toast-inline');
        const el = document.createElement('div'); el.className = 'builder-toast-item ' + type;
        el.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + '"></i> ' + msg;
        c.appendChild(el);
        setTimeout(function() { el.style.opacity = '0'; el.style.transition = 'opacity 0.3s'; setTimeout(function() { el.remove(); }, 300); }, 3000);
    }

    // ===== EVENT BINDINGS =====
    document.getElementById('btn-save-layout-inline').addEventListener('click', saveAllLayout);
    document.getElementById('btn-undo-inline').addEventListener('click', undo);
    document.getElementById('btn-zoom-fit-inline').addEventListener('click', zoomFit);
    document.getElementById('btn-zoom-reset-inline').addEventListener('click', function() { zoomLevel = 1; panX = 0; panY = 0; applyTransform(); });
    document.getElementById('btn-tambah-data-inline').addEventListener('click', function() {
        const r = body.getBoundingClientRect();
        addNode((r.width / 2 - panX) / zoomLevel - 100, (r.height / 2 - panY) / zoomLevel - 30, null);
    });
    // Photo upload & remove handlers
    document.getElementById('btn-upload-photo').addEventListener('click', function() {
        document.getElementById('edit-image-inline').click();
    });

    document.getElementById('edit-image-inline').addEventListener('change', function(e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) {
            showToast('Ukuran foto maksimal 5MB', 'error');
            this.value = '';
            return;
        }
        pendingImageFile = file;
        removeImageFlag = false;

        const reader = new FileReader();
        reader.onload = function(evt) {
            const photoImg = document.getElementById('panel-photo-img');
            const photoPlaceholder = document.getElementById('panel-photo-placeholder');
            const btnRemove = document.getElementById('btn-remove-photo');
            if (photoImg) {
                photoImg.src = evt.target.result;
                photoImg.style.display = 'block';
            }
            if (photoPlaceholder) photoPlaceholder.style.display = 'none';
            if (btnRemove) btnRemove.style.display = 'inline-flex';
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('btn-remove-photo').addEventListener('click', function() {
        pendingImageFile = null;
        removeImageFlag = true;
        const fileInput = document.getElementById('edit-image-inline');
        if (fileInput) fileInput.value = '';
        const photoImg = document.getElementById('panel-photo-img');
        const photoPlaceholder = document.getElementById('panel-photo-placeholder');
        if (photoImg) {
            photoImg.src = '';
            photoImg.style.display = 'none';
        }
        if (photoPlaceholder) photoPlaceholder.style.display = 'flex';
        this.style.display = 'none';
    });

    document.getElementById('btn-update-node-inline').addEventListener('click', function() {
        if (!selectedNodeId) return;
        const node = getNodeById(selectedNodeId); if (!node) return;
        const j = document.getElementById('edit-jabatan-inline').value.trim();
        if (!j) { showToast('Jabatan wajib diisi', 'error'); return; }
        node.jabatan  = j;
        node.nama     = document.getElementById('edit-nama-inline').value.trim()     || '-';
        node.nip      = document.getElementById('edit-nip-inline').value.trim()      || '-';
        node.pangkat  = document.getElementById('edit-pangkat-inline').value.trim()  || '-';
        node.golongan = document.getElementById('edit-golongan-inline').value.trim() || '-';
        const p = document.getElementById('edit-parent-inline').value;
        const pid = p ? parseInt(p) : null;
        if (pid && !wouldCreateCycle(node.id, pid)) node.parent_id = pid;
        else if (pid) { showToast('Sirkular!', 'error'); document.getElementById('edit-parent-inline').value = node.parent_id || ''; }
        else node.parent_id = null;

        const curImageFile = pendingImageFile;
        const curRemoveFlag = removeImageFlag;

        const btn = this;
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        pushUndo();
        renderNodes();
        selectNode(node.id, false);

        saveSingleNode(node, curImageFile, curRemoveFlag)
            .then(function() {
                pendingImageFile = null;
                removeImageFlag = false;
                showToast('Kotak berhasil diperbarui', 'success');
                updatePanel(node.id);
            })
            .catch(function(err) {
                showToast('Gagal: ' + (err.message || 'Terjadi kesalahan'), 'error');
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = origText;
            });
    });
    document.getElementById('btn-delete-node-inline').addEventListener('click', function() {
        if (selectedNodeIds.size > 1) deleteSelectedNodes();
        else if (selectedNodeId) deleteNode(selectedNodeId);
    });
    document.getElementById('btn-duplicate-node-inline').addEventListener('click', function() {
        if (selectedNodeId) duplicateNode(selectedNodeId);
    });

    document.addEventListener('keydown', function(e) {
        const inInput = e.target.closest('input,select,textarea');
        if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveAllLayout(); }
        if ((e.ctrlKey || e.metaKey) && e.key === 'z') { e.preventDefault(); undo(); }
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            selectedNodeIds.clear(); nodes.forEach(function(n) { selectedNodeIds.add(n.id); });
            selectedNodeId = nodes.length > 0 ? nodes[0].id : null;
            refreshNodeClasses();
            if (nodes.length > 1) updateMultiSelectPanel();
            else if (nodes.length === 1) { panel.classList.add('open'); updatePanel(nodes[0].id); }
        }
        if (e.key === 'Delete' && !inInput) {
            e.preventDefault();
            if (selectedNodeIds.size > 1) deleteSelectedNodes();
            else if (selectedNodeId) deleteNode(selectedNodeId);
        }
        if (e.key === 'Escape') {
            deselectAll();
            contextMenu.classList.remove('show');
            selectedConnectorKey = null;
            document.getElementById('connector-color-picker-inline').classList.remove('show');
            setPanelMode('idle');
            renderConnectors();
        }
        if (e.key === 'v' && !e.ctrlKey && !inInput) document.getElementById('tool-select-btn').click();
        if (e.key === 'c' && !e.ctrlKey && !inInput) document.getElementById('tool-connect-btn').click();
    });

    // ===== MODAL WAYPOINT EDITOR =====
    document.getElementById('wp-modal-close').addEventListener('click', closeWaypointEditor);
    document.getElementById('wp-modal-cancel').addEventListener('click', closeWaypointEditor);
    document.getElementById('wp-modal-save').addEventListener('click', saveWaypointEditor);
    // Click overlay to close
    document.getElementById('wp-modal-inline').addEventListener('click', function(e) {
        if (e.target === this) closeWaypointEditor();
    });

    // ===== INIT =====
    try {
        const serverData = @json($connectorData ?? []);
        if (serverData) {
            if (serverData.waypoints) {
                Object.entries(serverData.waypoints).forEach(function(e) {
                    if (Array.isArray(e[1]) && e[1].length > 0) connectorWaypoints.set(e[0], e[1]);
                });
            }
            if (serverData.colors) {
                Object.entries(serverData.colors).forEach(function(e) { connectorColors.set(e[0], e[1]); });
            }
            if (serverData.styles) {
                Object.entries(serverData.styles).forEach(function(e) { connectorStyles.set(e[0], e[1]); });
            }
            if (serverData.ports) {
                Object.entries(serverData.ports).forEach(function(e) { connectorPorts.set(e[0], e[1]); });
            }
        }
        // Keep localStorage updated with server data
        const d = {
            waypoints: Object.fromEntries(connectorWaypoints),
            colors: Object.fromEntries(connectorColors),
            styles: Object.fromEntries(connectorStyles),
            ports: Object.fromEntries(connectorPorts)
        };
        try { localStorage.setItem('struktur_connector_data', JSON.stringify(d)); } catch (ex) {}
    } catch (ex) {}

    renderNodes();
    if (nodes.length > 0) {
        const mnX = Math.min(...nodes.map(n => n.x || 0));
        const mnY = Math.min(...nodes.map(n => n.y || 0));
        const mxY = Math.max(...nodes.map(n => (n.y || 0) + 96));
        panX = 80 - mnX;
        panY = body.clientHeight / 2 - (mnY + mxY) / 2;
    }
    setZoom(1);
    pushUndo();
    resizeGrid();
    window.addEventListener('resize', resizeGrid);
});
</script>
@endpush
