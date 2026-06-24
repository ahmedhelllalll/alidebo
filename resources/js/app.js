import './bootstrap';

import Alpine from 'alpinejs';

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

import ApexCharts from 'apexcharts';
import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
window.ApexCharts = ApexCharts;
window.Chart = Chart;
window.Sortable = Sortable;

Alpine.start();
