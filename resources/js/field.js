import '../css/field.css'
import DetailField from './components/DetailField.vue'
import FormField from './components/FormField.vue'
import IndexField from './components/IndexField.vue'

window.Nova.booting((app) => {
    app.component('index-nova-items-field', IndexField)
    app.component('detail-nova-items-field', DetailField)
    app.component('form-nova-items-field', FormField)
})
