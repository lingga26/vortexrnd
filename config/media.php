<?php

return [
    // Folder fisik tempat video disimpan (tanpa /iklan). Atur di .env tiap lingkungan:
    //   produksi: /home/novasgn/domains/vortexsign.web.id/public_html/media
    //   staging : /home/novasgn/domains/staging.vortexsign.web.id/public_html/media
    //   lokal   : otomatis ke folder public/media
    'path' => env('MEDIA_PATH', public_path('media')),
];  