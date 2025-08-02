import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'

export function useMaterialTypes() {
  const authStore = useAuthStore()
  
  // State
  const materialTypes = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
  })
  
  // Filters
  const filters = reactive({
    search: '',
    status: '',
    category: '',
    unit_of_measure: '',
    min_price: '',
    max_price: '',
    sort_by: 'created_at',
    sort_direction: 'desc',
    per_page: 15
  })
  
  // Available options
  const categories = ref([])
  const unitsOfMeasure = ref([])
  const stats = ref({})
  
  // API Base URL
  const API_BASE = '/api/material-types'
  
  // Helper function to make authenticated requests
  const makeRequest = async (url, options = {}) => {
    const token = authStore.token
    if (!token) {
      throw new Error('Token di autenticazione non disponibile')
    }
    
    const defaultOptions = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    }
    
    const response = await fetch(url, { ...defaultOptions, ...options })
    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || `HTTP error! status: ${response.status}`)
    }
    
    return data
  }
  
  // Fetch material types with filters and pagination
  const fetchMaterialTypes = async (page = 1) => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams()
      
      // Add pagination
      params.append('page', page)
      params.append('per_page', filters.per_page)
      
      // Add filters
      if (filters.search) params.append('search', filters.search)
      if (filters.status) params.append('status', filters.status)
      if (filters.category) params.append('category', filters.category)
      if (filters.unit_of_measure) params.append('unit_of_measure', filters.unit_of_measure)
      if (filters.min_price) params.append('min_price', filters.min_price)
      if (filters.max_price) params.append('max_price', filters.max_price)
      
      // Add sorting
      params.append('sort_by', filters.sort_by)
      params.append('sort_direction', filters.sort_direction)
      
      const response = await makeRequest(`${API_BASE}?${params}`)
      
      if (response.success) {
        materialTypes.value = response.data
        pagination.value = response.pagination
      } else {
        throw new Error(response.message || 'Errore nel caricamento dei tipi di materiale')
      }
    } catch (err) {
      error.value = err.message
      console.error('Error fetching material types:', err)
    } finally {
      loading.value = false
    }
  }
  
  // Create new material type
  const createMaterialType = async (materialTypeData) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await makeRequest(API_BASE, {
        method: 'POST',
        body: JSON.stringify(materialTypeData)
      })
      
      if (response.success) {
        await fetchMaterialTypes(pagination.value.current_page)
        return response.data
      } else {
        throw new Error(response.message || 'Errore nella creazione del tipo di materiale')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  // Update material type
  const updateMaterialType = async (id, materialTypeData) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await makeRequest(`${API_BASE}/${id}`, {
        method: 'PUT',
        body: JSON.stringify(materialTypeData)
      })
      
      if (response.success) {
        await fetchMaterialTypes(pagination.value.current_page)
        return response.data
      } else {
        throw new Error(response.message || 'Errore nell\'aggiornamento del tipo di materiale')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  // Delete material type
  const deleteMaterialType = async (id) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await makeRequest(`${API_BASE}/${id}`, {
        method: 'DELETE'
      })
      
      if (response.success) {
        await fetchMaterialTypes(pagination.value.current_page)
        return true
      } else {
        throw new Error(response.message || 'Errore nell\'eliminazione del tipo di materiale')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  // Get single material type
  const getMaterialType = async (id) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await makeRequest(`${API_BASE}/${id}`)
      
      if (response.success) {
        return response.data
      } else {
        throw new Error(response.message || 'Errore nel caricamento del tipo di materiale')
      }
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }
  
  // Fetch categories
  const fetchCategories = async () => {
    try {
      const response = await makeRequest(`${API_BASE}/categories`)
      if (response.success) {
        categories.value = response.data
      }
    } catch (err) {
      console.error('Error fetching categories:', err)
    }
  }
  
  // Fetch units of measure
  const fetchUnitsOfMeasure = async () => {
    try {
      const response = await makeRequest(`${API_BASE}/units-of-measure`)
      if (response.success) {
        unitsOfMeasure.value = response.data
      }
    } catch (err) {
      console.error('Error fetching units of measure:', err)
    }
  }
  
  // Fetch statistics
  const fetchStats = async () => {
    try {
      const response = await makeRequest(`${API_BASE}/stats`)
      if (response.success) {
        stats.value = response.data
      }
    } catch (err) {
      console.error('Error fetching stats:', err)
    }
  }
  
  // Reset filters
  const resetFilters = () => {
    Object.assign(filters, {
      search: '',
      status: '',
      category: '',
      unit_of_measure: '',
      min_price: '',
      max_price: '',
      sort_by: 'created_at',
      sort_direction: 'desc',
      per_page: 15
    })
  }
  
  // Apply filters and refresh data
  const applyFilters = async () => {
    await fetchMaterialTypes(1)
  }
  
  // Change page
  const changePage = async (page) => {
    await fetchMaterialTypes(page)
  }
  
  // Load initial data
  const loadData = async () => {
    await Promise.all([
      fetchMaterialTypes(),
      fetchCategories(),
      fetchUnitsOfMeasure(),
      fetchStats()
    ])
  }
  
  return {
    // State
    materialTypes,
    loading,
    error,
    pagination,
    filters,
    categories,
    unitsOfMeasure,
    stats,
    
    // Methods
    fetchMaterialTypes,
    createMaterialType,
    updateMaterialType,
    deleteMaterialType,
    getMaterialType,
    fetchCategories,
    fetchUnitsOfMeasure,
    fetchStats,
    resetFilters,
    applyFilters,
    changePage,
    loadData
  }
}