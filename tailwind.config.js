import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
	content: [
		"./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
		"./storage/framework/views/*.php",
		"./resources/views/**/*.blade.php",
	],

	theme: {
		extend: {
			fontFamily: {
				sans: ["Inter", "Figtree", ...defaultTheme.fontFamily.sans],
			},
			colors: {
				// Sidebar
				"sidebar-dark": "#041C4A",
				"sidebar-light": "#0A2E73",
				// Primary buttons
				primary: "#2563EB",
				"primary-hover": "#1D4ED8",
				// AI info
				"ai-blue": "#3B82F6",
				// Compliance levels
				"high-bg": "#DCFCE7",
				"high-text": "#16A34A",
				"medium-bg": "#FEF3C7",
				"medium-text": "#F59E0B",
				"low-bg": "#FEE2E2",
				"low-text": "#EF4444",
				// Text
				"body-text": "#0F172A",
				"muted-text": "#64748B",
				// UI
				"border-light": "#E2E8F0",
				"bg-page": "#F8FAFC",
				"card-bg": "#FFFFFF",
			},
		},
	},

	plugins: [forms],
};
