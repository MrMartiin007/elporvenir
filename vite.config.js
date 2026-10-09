import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Tienda en línea (antes incrustados en las vistas)
                'resources/css/shop-home.css',
                'resources/css/shop-product.css',
                'resources/css/shop-layout.css',
                'resources/css/shop-portada.css',
                'resources/css/shop-buscador.css',
                'resources/css/shop-tema.css',
            ],
            refresh: true,
        }),
    ],
});
