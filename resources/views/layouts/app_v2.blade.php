<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $data['title'] ?? 'JEZ PRO' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Fonts (CFT Icons) -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('app/assets/fonts/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style-solid.css') }}" rel="stylesheet" type="text/css" />
    
    @stack('styles')
    
    <style>
        :root, :host {
        --color-red-50: oklch(97.1% .013 17.38);
        --color-red-100: oklch(93.6% .032 17.717);
        --color-red-200: oklch(88.5% .062 18.334);
        --color-red-300: oklch(80.8% .114 19.571);
        --color-red-400: oklch(70.4% .191 22.216);
        --color-red-500: oklch(63.7% .237 25.331);
        --color-red-500: oklch(57.7% .245 27.325);
        --color-red-700: oklch(50.5% .213 27.518);
        --color-red-800: oklch(44.4% .177 26.899);
        --color-red-900: oklch(39.6% .141 25.723);
        --color-red-950: oklch(25.8% .092 26.042);
        --color-orange-50: oklch(98% .016 73.684);
        --color-orange-100: oklch(95.4% .038 75.164);
        --color-orange-200: oklch(90.1% .076 70.697);
        --color-orange-300: oklch(83.7% .128 66.29);
        --color-orange-400: oklch(75% .183 55.934);
        --color-orange-500: oklch(70.5% .213 47.604);
        --color-orange-600: oklch(64.6% .222 41.116);
        --color-orange-700: oklch(55.3% .195 38.402);
        --color-orange-800: oklch(47% .157 37.304);
        --color-orange-900: oklch(40.8% .123 38.172);
        --color-orange-950: oklch(26.6% .079 36.259);
        --color-yellow-50: oklch(98.7% .026 102.212);
        --color-yellow-100: oklch(97.3% .071 103.193);
        --color-yellow-200: oklch(94.5% .129 101.54);
        --color-yellow-300: oklch(90.5% .182 98.111);
        --color-yellow-400: oklch(85.2% .199 91.936);
        --color-yellow-500: oklch(79.5% .184 86.047);
        --color-yellow-600: oklch(68.1% .162 75.834);
        --color-yellow-700: oklch(55.4% .135 66.442);
        --color-yellow-800: oklch(47.6% .114 61.907);
        --color-yellow-900: oklch(42.1% .095 57.708);
        --color-lime-200: oklch(93.8% .127 124.321);
        --color-lime-300: oklch(89.7% .196 126.665);
        --color-lime-400: oklch(84.1% .238 128.85);
        --color-lime-500: oklch(76.8% .233 130.85);
        --color-lime-600: oklch(64.8% .2 131.684);
        --color-lime-800: oklch(45.3% .124 130.933);
        --color-green-50: oklch(98.2% .018 155.826);
        --color-green-100: oklch(96.2% .044 156.743);
        --color-green-200: oklch(92.5% .084 155.995);
        --color-green-300: oklch(87.1% .15 154.449);
        --color-green-400: oklch(79.2% .209 151.711);
        --color-green-500: oklch(72.3% .219 149.579);
        --color-green-600: oklch(62.7% .194 149.214);
        --color-green-700: oklch(52.7% .154 150.069);
        --color-green-800: oklch(44.8% .119 151.328);
        --color-green-900: oklch(39.3% .095 152.535);
        --color-green-950: oklch(26.6% .065 152.934);
        --color-emerald-50: oklch(97.9% .021 166.113);
        --color-emerald-100: oklch(95% .052 163.051);
        --color-emerald-200: oklch(90.5% .093 164.15);
        --color-emerald-300: oklch(84.5% .143 164.978);
        --color-emerald-600: oklch(59.6% .145 163.225);
        --color-emerald-700: oklch(50.8% .118 165.612);
        --color-emerald-800: oklch(43.2% .095 166.913);
        --color-emerald-900: oklch(37.8% .077 168.94);
        --color-emerald-950: oklch(26.2% .051 172.552);
        --color-teal-50: oklch(98.4% .014 180.72);
        --color-teal-100: oklch(95.3% .051 180.801);
        --color-teal-200: oklch(91% .096 180.426);
        --color-teal-300: oklch(85.5% .138 181.071);
        --color-teal-400: oklch(77.7% .152 181.912);
        --color-teal-500: oklch(70.4% .14 182.503);
        --color-teal-600: oklch(60% .118 184.704);
        --color-teal-700: oklch(51.1% .096 186.391);
        --color-teal-800: oklch(43.7% .078 188.216);
        --color-cyan-50: oklch(98.4% .019 200.873);
        --color-cyan-100: oklch(95.6% .045 203.388);
        --color-cyan-200: oklch(91.7% .08 205.041);
        --color-cyan-300: oklch(86.5% .127 207.078);
        --color-cyan-400: oklch(78.9% .154 211.53);
        --color-cyan-500: oklch(71.5% .143 215.221);
        --color-cyan-600: oklch(60.9% .126 221.723);
        --color-cyan-700: oklch(52% .105 223.128);
        --color-cyan-800: oklch(45% .085 224.283);
        --color-cyan-900: oklch(39.8% .07 227.392);
        --color-cyan-950: oklch(30.2% .056 229.695);
        --color-sky-400: oklch(74.6% .16 232.661);
        --color-sky-500: oklch(68.5% .169 237.323);
        --color-sky-600: oklch(58.8% .158 241.966);
        --color-blue-50: oklch(97% .014 254.604);
        --color-blue-100: oklch(93.2% .032 255.585);
        --color-blue-200: oklch(88.2% .059 254.128);
        --color-blue-300: oklch(80.9% .105 251.813);
        --color-blue-400: oklch(70.7% .165 254.624);
        --color-blue-500: oklch(62.3% .214 259.815);
        --color-blue-600: oklch(54.6% .245 262.881);
        --color-blue-700: oklch(48.8% .243 264.376);
        --color-blue-800: oklch(42.4% .199 265.638);
        --color-blue-900: oklch(37.9% .146 265.522);
        --color-blue-950: oklch(28.2% .091 267.935);
        --color-indigo-50: oklch(96.2% .018 272.314);
        --color-indigo-100: oklch(93% .034 272.788);
        --color-indigo-200: oklch(87% .065 274.039);
        --color-indigo-300: oklch(78.5% .115 274.713);
        --color-indigo-400: oklch(67.3% .182 276.935);
        --color-indigo-500: oklch(58.5% .233 277.117);
        --color-indigo-600: oklch(51.1% .262 276.966);
        --color-indigo-700: oklch(45.7% .24 277.023);
        --color-indigo-800: oklch(39.8% .195 277.366);
        --color-indigo-900: oklch(35.9% .144 278.697);
        --color-indigo-950: oklch(25.7% .09 281.288);
        --color-purple-50: oklch(97.7% .014 308.299);
        --color-purple-100: oklch(94.6% .033 307.174);
        --color-purple-200: oklch(90.2% .063 306.703);
        --color-purple-300: oklch(82.7% .119 306.383);
        --color-purple-400: oklch(71.4% .203 305.504);
        --color-purple-500: oklch(62.7% .265 303.9);
        --color-purple-600: oklch(55.8% .288 302.321);
        --color-purple-700: oklch(49.6% .265 301.924);
        --color-purple-800: oklch(43.8% .218 303.724);
        --color-purple-900: oklch(38.1% .176 304.987);
        --color-fuchsia-500: oklch(66.7% .295 322.15);
        --color-fuchsia-600: oklch(59.1% .293 322.896);
        --color-pink-50: oklch(97.1% .014 343.198);
        --color-pink-100: oklch(94.8% .028 342.258);
        --color-pink-200: oklch(89.9% .061 343.231);
        --color-pink-300: oklch(82.3% .12 346.018);
        --color-pink-400: oklch(71.8% .202 349.761);
        --color-pink-500: oklch(65.6% .241 354.308);
        --color-pink-600: oklch(59.2% .249 .584);
        --color-pink-700: oklch(52.5% .223 3.958);
        --color-pink-800: oklch(45.9% .187 3.815);
        --color-pink-900: oklch(40.8% .153 2.432);
        --color-pink-950: oklch(28.4% .109 3.907);
        --color-rose-50: oklch(96.9% .015 12.422);
        --color-rose-100: oklch(94.1% .03 12.58);
        --color-rose-200: oklch(89.2% .058 10.001);
        --color-rose-300: oklch(81% .117 11.638);
        --color-rose-500: oklch(64.5% .246 16.439);
        --color-rose-700: oklch(51.4% .222 16.935);
        --color-rose-800: oklch(45.5% .188 13.697);
        --color-rose-900: oklch(41% .159 10.272);
        --color-rose-950: oklch(27.1% .105 12.094);
        --color-slate-50: oklch(98.4% .003 247.858);
        --color-slate-100: oklch(96.8% .007 247.896);
        --color-slate-200: oklch(92.9% .013 255.508);
        --color-slate-300: oklch(86.9% .022 252.894);
        --color-slate-400: oklch(70.4% .04 256.788);
        --color-slate-500: oklch(55.4% .046 257.417);
        --color-slate-600: oklch(44.6% .043 257.281);
        --color-slate-700: oklch(37.2% .044 257.287);
        --color-slate-800: oklch(27.9% .041 260.031);
        --color-slate-900: oklch(20.8% .042 265.755);
        --color-slate-950: oklch(12.9% .042 264.695);
        --color-gray-50: oklch(98.5% .002 247.839);
        --color-gray-100: oklch(96.7% .003 264.542);
        --color-gray-200: oklch(92.8% .006 264.531);
        --color-gray-300: oklch(87.2% .01 258.338);
        --color-gray-400: oklch(70.7% .022 261.325);
        --color-gray-500: oklch(55.1% .027 264.364);
        --color-gray-600: oklch(44.6% .03 256.802);
        --color-gray-700: oklch(37.3% .034 259.733);
        --color-gray-800: oklch(27.8% .033 256.848);
        --color-gray-900: oklch(21% .034 264.665);
        --color-gray-950: oklch(13% .028 261.692);
        --color-zinc-50: oklch(98.5% 0 0);
        --color-zinc-100: oklch(96.7% .001 286.375);
        --color-zinc-200: oklch(92% .004 286.32);
        --color-zinc-300: oklch(87.1% .006 286.286);
        --color-zinc-400: oklch(70.5% .015 286.067);
        --color-zinc-500: oklch(55.2% .016 285.938);
        --color-zinc-600: oklch(44.2% .017 285.786);
        --color-zinc-700: oklch(37% .013 285.805);
        --color-zinc-800: oklch(27.4% .006 286.033);
        --color-zinc-900: oklch(21% .006 285.885);
        --color-zinc-950: oklch(14.1% .005 285.823);
        --color-neutral-50: oklch(98.5% 0 0);
        --color-neutral-100: oklch(97% 0 0);
        --color-neutral-200: oklch(92.2% 0 0);
        --color-neutral-300: oklch(87% 0 0);
        --color-neutral-400: oklch(70.8% 0 0);
        --color-neutral-500: oklch(55.6% 0 0);
        --color-neutral-600: oklch(43.9% 0 0);
        --color-neutral-700: oklch(37.1% 0 0);
        --color-neutral-800: oklch(26.9% 0 0);
        --color-neutral-900: oklch(20.5% 0 0);
        --color-neutral-950: oklch(14.5% 0 0);
        --color-stone-50: oklch(98.5% .001 106.423);
        --color-stone-100: oklch(97% .001 106.424);
        --color-stone-200: oklch(92.3% .003 48.717);
        --color-stone-300: oklch(86.9% .005 56.366);
        --color-stone-400: oklch(70.9% .01 56.259);
        --color-stone-500: oklch(55.3% .013 58.071);
        --color-stone-600: oklch(44.4% .011 73.639);
        --color-stone-700: oklch(37.4% .01 67.558);
        --color-stone-800: oklch(26.8% .007 34.298);
        --color-stone-900: oklch(21.6% .006 56.043);
        --color-stone-950: oklch(14.7% .004 49.25);
        --color-white: #fff;
        --spacing: .25rem;
        --breakpoint-md: 48rem;
        --breakpoint-lg: 64rem;
        --breakpoint-xl: 80rem;
        --breakpoint-2xl: 96rem;
        --container-2xs: 18rem;
        --container-xs: 20rem;
        --container-sm: 24rem;
        --container-md: 28rem;
        --container-lg: 32rem;
        --container-xl: 36rem;
        --container-2xl: 42rem;
        --container-4xl: 56rem;
        --container-7xl: 80rem;
        --text-xs: .75rem;
        --text-xs--line-height: calc(1 / .75);
        --text-sm: .875rem;
        --text-sm--line-height: calc(1.25 / .875);
        --text-base: 1rem;
        --text-base--line-height: calc(1.5 / 1);
        --text-lg: 1.125rem;
        --text-lg--line-height: calc(1.75 / 1.125);
        --text-xl: 1.25rem;
        --text-xl--line-height: calc(1.75 / 1.25);
        --text-2xl: 1.5rem;
        --text-2xl--line-height: calc(2 / 1.5);
        --text-3xl: 1.875rem;
        --text-3xl--line-height: calc(2.25 / 1.875);
        --text-4xl: 2.25rem;
        --text-4xl--line-height: calc(2.5 / 2.25);
        --text-5xl: 3rem;
        --text-5xl--line-height: 1;
        --text-6xl: 3.75rem;
        --text-6xl--line-height: 1;
        --text-7xl: 4.5rem;
        --text-7xl--line-height: 1;
        --text-8xl: 6rem;
        --text-8xl--line-height: 1;
        --text-9xl: 8rem;
        --text-9xl--line-height: 1;
        --font-weight-thin: 100;
        --font-weight-extralight: 200;
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;
        --font-weight-extrabold: 800;
        --font-weight-black: 900;
        --tracking-tighter: -.8px;
        --tracking-tight: -.4px;
        --tracking-normal: 0em;
        --tracking-wide: .025em;
        --tracking-wider: .05em;
        --tracking-widest: .1em;
        --leading-tight: 1.25;
        --leading-normal: 1.5;
        --leading-relaxed: 1.625;
        --leading-loose: 2;
        --radius-xs: 4px;
        --radius-sm: 6px;
        --radius-md: .375rem;
        --radius-lg: 16px;
        --radius-xl: .75rem;
        --ease-in: cubic-bezier(.4, 0, 1, 1);
        --ease-out: cubic-bezier(0, 0, .2, 1);
        --ease-in-out: cubic-bezier(.4, 0, .2, 1);
        --animate-spin: spin 1s linear infinite;
        --animate-pulse: pulse 2s cubic-bezier(.4, 0, .6, 1) infinite;
        --blur-xs: 4px;
        --default-transition-duration: .15s;
        --default-transition-timing-function: cubic-bezier(.4, 0, .2, 1);
        --radius: 8px;
        --font-body: "Inter", "ui-sans-serif", "system-ui", "-apple-system", "system-ui", "Segoe UI", "Roboto", "Helvetica Neue", "Arial", "Noto Sans", "sans-serif", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        --spacing-8xl: 90rem;
        --leading-9: 36px;
        --leading-8: 32px;
        --leading-6: 24px;
        --leading-4: 16px;
        --leading-none: 1px;
        --leading-5: 20px;
        --radius-0: 0px;
        --radius-xxs: 2px;
        --radius-base: 10px;
        --color-body: var(--color-gray-600);
        --color-body-subtle: var(--color-gray-500);
        --color-heading: var(--color-gray-900);
        --color-fg-brand-subtle: var(--color-blue-200);
        --color-fg-brand: var(--color-blue-700);
        --color-fg-brand-strong: var(--color-blue-900);
        --color-fg-success: var(--color-emerald-700);
        --color-fg-success-strong: var(--color-emerald-900);
        --color-fg-danger: var(--color-rose-700);
        --color-fg-danger-strong: var(--color-rose-900);
        --color-fg-warning-subtle: var(--color-orange-600);
        --color-fg-warning: var(--color-orange-900);
        --color-fg-yellow: var(--color-yellow-400);
        --color-fg-disabled: var(--color-gray-400);
        --color-fg-purple: var(--color-purple-600);
        --color-fg-cyan: var(--color-cyan-600);
        --color-fg-indigo: var(--color-indigo-600);
        --color-fg-pink: var(--color-pink-600);
        --color-fg-lime: var(--color-lime-600);
        --color-neutral-primary-soft: var(--color-white);
        --color-neutral-primary: var(--color-white);
        --color-neutral-primary-medium: var(--color-white);
        --color-neutral-primary-strong: var(--color-white);
        --color-neutral-secondary-soft: var(--color-gray-50);
        --color-neutral-secondary: var(--color-gray-50);
        --color-neutral-secondary-medium: var(--color-gray-50);
        --color-neutral-secondary-strong: var(--color-gray-50);
        --color-neutral-secondary-strongest: var(--color-gray-50);
        --color-neutral-tertiary-soft: var(--color-gray-100);
        --color-neutral-tertiary: var(--color-gray-100);
        --color-neutral-tertiary-medium: var(--color-gray-100);
        --color-neutral-quaternary: var(--color-gray-200);
        --color-neutral-quaternary-medium: var(--color-gray-200);
        --color-gray: var(--color-gray-300);
        --color-brand-softer: var(--color-blue-50);
        --color-brand-soft: var(--color-blue-100);
        --color-brand: var(--color-blue-700);
        --color-brand-medium: var(--color-blue-200);
        --color-brand-strong: var(--color-blue-800);
        --color-success-soft: var(--color-emerald-50);
        --color-success: var(--color-emerald-700);
        --color-success-medium: var(--color-emerald-100);
        --color-success-strong: var(--color-emerald-800);
        --color-danger-soft: var(--color-rose-50);
        --color-danger: var(--color-rose-700);
        --color-danger-medium: var(--color-rose-100);
        --color-danger-strong: var(--color-rose-800);
        --color-warning-soft: var(--color-orange-50);
        --color-warning: var(--color-orange-500);
        --color-warning-medium: var(--color-orange-100);
        --color-warning-strong: var(--color-orange-700);
        --color-dark-soft: var(--color-gray-800);
        --color-dark: var(--color-gray-800);
        --color-dark-strong: var(--color-gray-900);
        --color-disabled: var(--color-gray-100);
        --color-purple: var(--color-purple-500);
        --color-sky: var(--color-sky-500);
        --color-teal: var(--color-teal-600);
        --color-pink: var(--color-pink-600);
        --color-cyan: var(--color-cyan-500);
        --color-fuchsia: var(--color-fuchsia-600);
        --color-indigo: var(--color-indigo-600);
        --color-orange: var(--color-orange-400);
        --color-buffer: var(--color-white);
        --color-buffer-medium: var(--color-white);
        --color-buffer-strong: var(--color-white);
        --color-muted: var(--color-gray-50);
        --color-light-subtle: var(--color-gray-100);
        --color-light: var(--color-gray-100);
        --color-light-medium: var(--color-gray-100);
        --color-default-subtle: var(--color-gray-200);
        --color-default: var(--color-gray-200);
        --color-default-medium: var(--color-gray-200);
        --color-default-strong: var(--color-gray-200);
        --color-success-subtle: var(--color-emerald-200);
        --color-danger-subtle: var(--color-rose-200);
        --color-warning-subtle: var(--color-orange-200);
        --color-brand-subtle: var(--color-blue-200);
        --color-brand-light: var(--color-blue-600);
        --color-dark-subtle: var(--color-gray-800);
        --color-dark-backdrop: var(--color-gray-950);
        --squares-pattern-light-url: url(/docs/images/patterns/squares.svg);
        --squares-pattern-dark-url: url(/docs/images/patterns/squares-dark.svg);
        --color-shiki-fg-brand: #79b8ff;
        --color-shiki-fg-brand-subtle: #9ecbff;
    }

        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            flex-direction: column;
        }
        main.flex-1 {
            padding-bottom: 6rem !important;
            font-size: .9rem !important;
        }
        .text-sm {
            font-size: .825rem !important;
        }
        .bg-login {
            background: #f8f8f8;
        }
        .sidebar-item:hover {
            background-color: #f3f4f6;
        }
        .sidebar-item.active {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        .ml-72-plus {
            margin-left: 18.5rem;
        }
        .ml-32-plus {
            margin-left: 8.5rem;
        }

        
        /* Sidebar Toggle Transitions */
        #sidebar {
            transition: width 0.3s ease-in-out;
        }
        
        .sidebar-toggle-icon {
            transition: transform 0.3s ease-in-out;
        }
        
        .rotate-180 {
            transform: rotate(180deg);
        }
        
        .sidebar-menu-text,
        .sidebar-section-title,
        .sidebar-text-visible,
        .sidebar-icon-only {
            transition: opacity 0.2s ease-in-out;
        }
        
        /* Collapsed sidebar styles */
        .sidebar-collapsed .sidebar-item {
            justify-content: center !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        .sidebar-collapsed .sidebar-menu-text,
        .sidebar-collapsed .sidebar-section-title,
        .sidebar-collapsed .sidebar-text-visible {
            display: none;
        }
        
        /* Tooltip for collapsed menu items */
        .sidebar-collapsed .sidebar-item {
            position: relative;
        }
        
        .sidebar-collapsed .sidebar-item:hover::after {
            content: attr(title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 0.5rem;
            padding: 0.5rem 0.75rem;
            background-color: #1f2937;
            color: white;
            font-size: 0.875rem;
            white-space: nowrap;
            border-radius: 0.5rem;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Main content transition */
        main {
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* Prevent horizontal scroll on body */
        html, body {
            overflow-x: hidden;
        }
        
        /* Main content area - prevent horizontal scroll */
        main {
            overflow-x: hidden;
            max-width: 100%;
        }
        
        /* Submenu arrow rotation */
        .announcement-arrow {
            transition: transform 0.2s ease-in-out;
        }
        
        /* Simple-DataTables Flowbite Styling */
        .datatable-wrapper {
            width: 100%;
        }
        
        .datatable-top,
        .datatable-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
        }
        
        .datatable-search {
            position: relative;
        }
        
        .datatable-search input {
            display: block;
            width: 100%;
            padding: 0.625rem 2.5rem 0.625rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .datatable-search input:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
            border-color: #3b82f6;
        }
        
        .datatable-info {
            font-size: 0.875rem;
            color: #4b5563;
        }
        .datatable-table thead {
            color: var(--color-gray-900);
            background-color: var(--color-gray-100);
            border-bottom: 1px solid var(--color-gray-200);
            font-size: .75rem;
        }
        .datatable-pagination {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .py-3 {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }
        
        .datatable-pagination-list {
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            height: 2rem !important;
            font-size: .875rem;
            display: flex;
        }
        
        
        .datatable-pagination-list li {
            list-style: none;
        }
        
        .datatable-pagination-list a,
        .datatable-pagination-list button {

            color: var(--color-gray-600);
            background-color: var(--color-gray-50) !important  ;
            border-top: 1px solid var(--color-gray-200) !important;
            border-bottom: 1px solid var(--color-gray-200) !important;
            border-right: 1px solid var(--color-gray-200) !important;
            align-items: center;
            height: 2rem;
            padding-left: .75rem;
            padding-right: .75rem;
            font-size: .875rem;
            font-weight: 500;
            display: flex;
            cursor: pointer;
            text-decoration: none;
        }
        .datatable-pagination-list-item:first-of-type button {
            border-top-left-radius: var(--radius-base) !important;
            border-bottom-left-radius: var(--radius-base) !important;
            border-left: 1px solid var(--color-gray-200);
        }
        .datatable-pagination-list-item:last-of-type button {
            border-top-right-radius: var(--radius-base) !important;
            border-bottom-right-radius: var(--radius-base) !important;
            border-right: 1px solid var(--color-gray-200);
        }
        
        .datatable-pagination-list a:hover:not(.active):not(:disabled),
        .datatable-pagination-list button:hover:not(.active):not(:disabled) {
            background-color: #f9fafb;
            color: #111827;
        }
        .datatable-pagination-list-item.datatable-active button {
            background-color: var(--color-gray-200) !important;
            color: var(--color-gray-900) ;
            border-color: var(--color-gray-400) ;
        }
        
        .datatable-pagination-list a.active,
        .datatable-pagination-list button.active {
            background-color: var(--color-gray-300) ;
            color: var(--color-gray-900) ;
            border-color: var(--color-gray-400) ;
        }
        
        .datatable-pagination-list a.active:hover,
        .datatable-pagination-list button.active:hover {
            background-color: var(--color-gray-300) ;
        }
        
        .datatable-pagination-list a:disabled,
        .datatable-pagination-list button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .datatable-pagination-list a:disabled:hover,
        .datatable-pagination-list button:disabled:hover {
            background-color: white;
        }
        
        .datatable-selector {
            background-color: var(--color-gray-100) !important;
            color: var(--color-gray-900) !important;
            min-width: 4rem;
            margin-right: .25rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid var(--color-gray-200);
            border-radius: 0.5rem;
            background-color: white;
        }
        
        .datatable-selector:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
            border-color: #3b82f6;
        }
        
        .datatable-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .datatable-table thead {
            background-color: #f9fafb;
        }
        
        .datatable-table thead th {
            padding: 0.75rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .datatable-table tbody {
            background-color: white;
        }
        
        .datatable-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .datatable-table tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .datatable-table tbody td {
            padding: 1rem 1.5rem;
            white-space: nowrap;
            font-size: 0.875rem;
        }
        
        .datatable-sorter {
            display: inline-block;
            margin-left: 0.5rem;
            cursor: pointer;
            color: #9ca3af;
        }
        
        .datatable-sorter:hover {
            color: #4b5563;
        }
        
        .datatable-empty {
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        /* Override simple-datatables default styles */
        .datatable-wrapper .datatable-table {
            margin-top: 0;
            margin-bottom: 0;
        }
        
        .datatable-wrapper .datatable-top {
            margin-bottom: 1rem;
        }
        
        .datatable-wrapper .datatable-bottom {
            margin-top: 1rem;
        }
        
        /* Ensure table container maintains styling */
        .datatable-wrapper table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        /* Fix for table inside rounded container */
        .bg-white.rounded-xl .datatable-wrapper {
            overflow: hidden;
        }
        
        .bg-white.rounded-lg .datatable-wrapper {
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-login">
    
    <!-- Top Bar -->
    @include('layouts.partials_v2.topbar')
    
    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.partials_v2.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 px-6 pb-6 pt-2 ml-72 overflow-y-auto" style="height: calc(100vh - 80px); overflow-x: hidden;">
            @yield('content')
        </main>
    </div>
    
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>    
    
    <!-- DataTables (after jQuery) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.js"></script>
    
    <!-- Chart.js (for charts) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
</body>
</html>

