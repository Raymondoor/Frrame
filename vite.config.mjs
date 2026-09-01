import { defineConfig } from 'vite';

export default defineConfig({
	base : '',
	publicDir: false,
	// Enable the manifest file for production
	build: {
		manifest: true,
		outDir: 'public/dist',
		emptyOutDir: true,
		rollupOptions: {
			input: {
				app: 'resource/asset/script/app.js',
			},
		},
	},
	server: {
		cors: true,
		strictPort: true,
		origin: 'http://localhost:5173',
		port: 5173,
		hmr: {
			overlay: false
		}
	},
});