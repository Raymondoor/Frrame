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
				public: 'resource/asset/script/public.js',
				admin: 'resource/asset/script/admin.js',
			},
		},
	},
	server: {
		// Allows CORS requests from your local PHP server domain
		cors: true,
		strictPort: true,
		port: 5173,
		hmr: {
			overlay: false
		},
		watch: {
			ignored: ['**/public/**'],
		},
	},
});