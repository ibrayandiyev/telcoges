import mutations from './mutations'
import * as actions from './actions'
import * as getters from './getters'

const initialState = {
  invoices: [],
  invoiceTemplateId: 1,
  defaultTemplate: false,
  selectedInvoices: [],
  selectAllField: false,
  totalInvoices: 0,
  selectedCustomer: null,
  selectedNote: null,
  selectedItem: null,
}

export default {
  namespaced: true,

  state: initialState,

  getters: getters,

  actions: actions,

  mutations: mutations,
}
