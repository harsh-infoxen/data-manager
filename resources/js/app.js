
import './bootstrap';
import { createApp } from 'vue';
import vuetify from "./vuetify";



const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
import PortalLayout from './components/PortalLayout.vue';
import DashboardComponent from './components/DashboardComponent.vue';
import FilterSearch from './components/FilterSearch.vue';

app.component('example-component', ExampleComponent);
app.component('portal-layout',PortalLayout);
app.component('dashboard-component',DashboardComponent);
app.component('filter-search',FilterSearch);
app.use(vuetify);
app.mount('#app');
