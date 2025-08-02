<template>
  <div class="material-types-page">
    <!-- Page Header -->
    <div class="bg-white shadow">
      <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
        <div class="py-6 md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <div class="flex items-center">
              <div>
                <div class="flex items-center">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                  </svg>
                  <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                    Tipi di Materiale
                  </h1>
                </div>
                <dl class="mt-6 flex flex-col sm:ml-3 sm:mt-1 sm:flex-row sm:flex-wrap">
                  <dt class="sr-only">Totale</dt>
                  <dd class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ pagination.total }} materiali totali
                  </dd>
                  <dt class="sr-only">Attivi</dt>
                  <dd class="flex items-center text-sm text-gray-500 font-medium capitalize">
                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ stats.active || 0 }} attivi
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
            <CanAccess permission="create.material-types">
              <button
                @click="openCreateModal"
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Aggiungi Materiale
              </button>
            </CanAccess>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="py-8">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Filtri</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <!-- Search -->
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Cerca</label>
                <input
                  id="search"
                  v-model="filters.search"
                  type="text"
                  placeholder="Nome, descrizione, categoria..."
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @input="debounceSearch"
                />
              </div>

              <!-- Status -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Stato</label>
                <select
                  id="status"
                  v-model="filters.status"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="">Tutti</option>
                  <option value="active">Attivo</option>
                  <option value="inactive">Inattivo</option>
                </select>
              </div>

              <!-- Category -->
              <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                <select
                  id="category"
                  v-model="filters.category"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="">Tutte</option>
                  <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                  </option>
                </select>
              </div>

              <!-- Unit of Measure -->
              <div>
                <label for="unit" class="block text-sm font-medium text-gray-700">Unità di Misura</label>
                <select
                  id="unit"
                  v-model="filters.unit_of_measure"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="">Tutte</option>
                  <option v-for="unit in unitsOfMeasure" :key="unit" :value="unit">
                    {{ unit }}
                  </option>
                </select>
              </div>

              <!-- Price Range -->
              <div>
                <label for="min_price" class="block text-sm font-medium text-gray-700">Prezzo Min (€)</label>
                <input
                  id="min_price"
                  v-model="filters.min_price"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @input="debouncePrice"
                />
              </div>

              <div>
                <label for="max_price" class="block text-sm font-medium text-gray-700">Prezzo Max (€)</label>
                <input
                  id="max_price"
                  v-model="filters.max_price"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="999999.99"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @input="debouncePrice"
                />
              </div>

              <!-- Sort -->
              <div>
                <label for="sort_by" class="block text-sm font-medium text-gray-700">Ordina per</label>
                <select
                  id="sort_by"
                  v-model="filters.sort_by"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="created_at">Data Creazione</option>
                  <option value="name">Nome</option>
                  <option value="category">Categoria</option>
                  <option value="default_price">Prezzo</option>
                  <option value="status">Stato</option>
                </select>
              </div>

              <div>
                <label for="sort_direction" class="block text-sm font-medium text-gray-700">Direzione</label>
                <select
                  id="sort_direction"
                  v-model="filters.sort_direction"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="desc">Decrescente</option>
                  <option value="asc">Crescente</option>
                </select>
              </div>
            </div>

            <!-- Filter Actions -->
            <div class="mt-4 flex justify-between">
              <button
                @click="resetFilters"
                type="button"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Filtri
              </button>
              
              <div class="flex items-center space-x-2">
                <label for="per_page" class="text-sm font-medium text-gray-700">Elementi per pagina:</label>
                <select
                  id="per_page"
                  v-model="filters.per_page"
                  class="border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="10">10</option>
                  <option value="15">15</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Material Types Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Lista Tipi di Materiale
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              Gestisci i tipi di materiale disponibili nel sistema.
            </p>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="px-4 py-8 text-center">
            <div class="inline-flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Caricamento...
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="px-4 py-8 text-center">
            <div class="text-red-600">
              <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Errore nel caricamento</h3>
              <p class="mt-1 text-sm text-gray-500">{{ error }}</p>
              <div class="mt-6">
                <button
                  @click="loadData"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                  Riprova
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else-if="materialTypes.length === 0" class="px-4 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nessun materiale trovato</h3>
            <p class="mt-1 text-sm text-gray-500">Inizia creando un nuovo tipo di materiale.</p>
            <div class="mt-6">
              <CanAccess permission="create.material-types">
                <button
                  @click="openCreateModal"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                  <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Aggiungi Primo Materiale
                </button>
              </CanAccess>
            </div>
          </div>

          <!-- Material Types List -->
          <ul v-else class="divide-y divide-gray-200">
            <li v-for="material in materialTypes" :key="material.id" class="px-4 py-4 hover:bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center">
                    <div class="flex-1">
                      <div class="flex items-center">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ material.name }}
                        </p>
                        <span
                          class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusClass(material.status)"
                        >
                          {{ material.status === 'active' ? 'Attivo' : 'Inattivo' }}
                        </span>
                      </div>
                      <div class="mt-1 flex items-center text-sm text-gray-500">
                        <span v-if="material.category" class="mr-4">
                          <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                          </svg>
                          {{ material.category }}
                        </span>
                        <span class="mr-4">
                          <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                          </svg>
                          {{ material.unit_of_measure }}
                        </span>
                        <span v-if="material.default_price" class="mr-4">
                          <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                          </svg>
                          € {{ formatPrice(material.default_price) }}
                        </span>
                      </div>
                      <p v-if="material.description" class="mt-1 text-sm text-gray-500 truncate">
                        {{ material.description }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <CanAccess permission="edit.material-types">
                    <button
                      @click="openEditModal(material)"
                      class="p-1 text-gray-400 hover:text-blue-600"
                      title="Modifica"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                  </CanAccess>
                  <CanAccess permission="delete.material-types">
                    <button
                      @click="confirmDelete(material)"
                      class="p-1 text-gray-400 hover:text-red-600"
                      title="Elimina"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </CanAccess>
                </div>
              </div>
            </li>
          </ul>

          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Precedente
              </button>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Successiva
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando
                  <span class="font-medium">{{ pagination.from }}</span>
                  a
                  <span class="font-medium">{{ pagination.to }}</span>
                  di
                  <span class="font-medium">{{ pagination.total }}</span>
                  risultati
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page <= 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Precedente</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                  
                  <template v-for="page in getVisiblePages()" :key="page">
                    <button
                      v-if="page !== '...'"
                      @click="changePage(page)"
                      :class="[
                        page === pagination.current_page
                          ? 'z-10 bg-primary-50 border-primary-500 text-primary-600'
                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                      ]"
                    >
                      {{ page }}
                    </button>
                    <span
                      v-else
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                    >
                      ...
                    </span>
                  </template>
                  
                  <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span class="sr-only">Successiva</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Material Type Modal -->
    <MaterialTypeModal
      :show="showModal"
      :material="selectedMaterial"
      :categories="categories"
      :units-of-measure="unitsOfMeasure"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      title="Elimina Tipo di Materiale"
      :message="`Sei sicuro di voler eliminare il tipo di materiale '${materialToDelete?.name}'? Questa azione non può essere annullata.`"
      confirm-text="Elimina"
      confirm-class="bg-red-600 hover:bg-red-700 focus:ring-red-500"
      @confirm="handleDelete"
      @cancel="closeDeleteModal"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useMaterialTypes } from '@/composables/useMaterialTypes'
import CanAccess from '@/components/CanAccess.vue'
import MaterialTypeModal from '@/components/MaterialTypeModal.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

// Composables
const {
  materialTypes,
  loading,
  error,
  pagination,
  filters,
  categories,
  unitsOfMeasure,
  stats,
  loadData,
  applyFilters,
  resetFilters,
  changePage,
  createMaterialType,
  updateMaterialType,
  deleteMaterialType
} = useMaterialTypes()

// Modal state
const showModal = ref(false)
const selectedMaterial = ref(null)
const showDeleteModal = ref(false)
const materialToDelete = ref(null)

// Debounce timers
let searchTimeout = null
let priceTimeout = null

// Methods
const openCreateModal = () => {
  selectedMaterial.value = null
  showModal.value = true
}

const openEditModal = (material) => {
  selectedMaterial.value = { ...material }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedMaterial.value = null
}

const handleSave = async (materialData) => {
  try {
    if (selectedMaterial.value?.id) {
      await updateMaterialType(selectedMaterial.value.id, materialData)
    } else {
      await createMaterialType(materialData)
    }
    closeModal()
  } catch (error) {
    console.error('Error saving material type:', error)
  }
}

const confirmDelete = (material) => {
  materialToDelete.value = material
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  materialToDelete.value = null
}

const handleDelete = async () => {
  if (materialToDelete.value) {
    try {
      await deleteMaterialType(materialToDelete.value.id)
      closeDeleteModal()
    } catch (error) {
      console.error('Error deleting material type:', error)
    }
  }
}

// Utility methods
const getStatusClass = (status) => {
  return status === 'active'
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
}

const formatPrice = (price) => {
  if (!price) return '0,00'
  return new Intl.NumberFormat('it-IT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(price)
}

const getVisiblePages = () => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    } else if (current >= last - 3) {
      pages.push(1)
      pages.push('...')
      for (let i = last - 4; i <= last; i++) {
        pages.push(i)
      }
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    }
  }
  
  return pages
}

// Debounced search
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

// Debounced price filter
const debouncePrice = () => {
  clearTimeout(priceTimeout)
  priceTimeout = setTimeout(() => {
    applyFilters()
  }, 1000)
}

// Reset filters and apply
const resetFiltersAndApply = async () => {
  resetFilters()
  await applyFilters()
}

// Load data on mount
onMounted(() => {
  loadData()
})
</script>