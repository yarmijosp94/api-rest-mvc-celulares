<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { OrdenReparacion } from '../../types'

const props = defineProps<{
  ordenes: {
    data: OrdenReparacion[]
    links: any[]
    meta: {
      total: number
      per_page: number
      current_page: number
    }
  }
  stats: {
    total: number
    pendientes: number
    en_proceso: number
    completadas: number
    canceladas: number
    requieren_aprobacion: number
  }
  estados: Record<string, string>
}>()

const searchQuery = ref('')
const selectedEstado = ref<string>('')

const estadosColor = {
  recibido: 'gray',
  en_diagnostico: 'blue',
  diagnosticado: 'cyan',
  pendiente_aprobacion: 'yellow',
  aprobado: 'green',
  rechazado: 'red',
  en_reparacion: 'indigo',
  reparado: 'emerald',
  entregado: 'teal',
  cancelado: 'red'
}

const prioridadColor = {
  baja: 'gray',
  media: 'blue',
  alta: 'orange',
  urgente: 'red'
}

const filteredOrdenes = computed(() => {
  return props.ordenes.data.filter(orden => {
    const matchSearch = !searchQuery.value || 
      orden.numeroOrden.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      orden.cliente?.razonSocial.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    const matchEstado = !selectedEstado.value || orden.estado === selectedEstado.value
    
    return matchSearch && matchEstado
  })
})

const handleCreate = () => {
  router.visit(route('ordenes-reparacion.create'))
}

const handleView = (orden: OrdenReparacion) => {
  router.visit(route('ordenes-reparacion.show', orden.id))
}

const handleEdit = (orden: OrdenReparacion) => {
  router.visit(route('ordenes-reparacion.edit', orden.id))
}
</script>

<template>
  <div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Órdenes de Reparación</h1>
      <button @click="handleCreate" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Nueva Orden
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-gray-600 text-sm">Total Órdenes</div>
        <div class="text-3xl font-bold">{{ stats.total }}</div>
      </div>
      <div class="bg-blue-50 rounded-lg shadow p-4">
        <div class="text-blue-800 text-sm">Pendientes</div>
        <div class="text-3xl font-bold text-blue-600">{{ stats.pendientes }}</div>
      </div>
      <div class="bg-indigo-50 rounded-lg shadow p-4">
        <div class="text-indigo-800 text-sm">En Proceso</div>
        <div class="text-3xl font-bold text-indigo-600">{{ stats.en_proceso }}</div>
      </div>
      <div class="bg-green-50 rounded-lg shadow p-4">
        <div class="text-green-800 text-sm">Completadas</div>
        <div class="text-3xl font-bold text-green-600">{{ stats.completadas }}</div>
      </div>
      <div class="bg-red-50 rounded-lg shadow p-4">
        <div class="text-red-800 text-sm">Canceladas</div>
        <div class="text-3xl font-bold text-red-600">{{ stats.canceladas }}</div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
      <div class="flex gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por número de orden o cliente..."
          class="flex-1 px-4 py-2 border rounded-lg"
        />
        <select v-model="selectedEstado" class="px-4 py-2 border rounded-lg">
          <option value="">Todos los estados</option>
          <option value="recibido">Recibido</option>
          <option value="en_diagnostico">En Diagnóstico</option>
          <option value="diagnosticado">Diagnosticado</option>
          <option value="pendiente_aprobacion">Pendiente Aprobación</option>
          <option value="aprobado">Aprobado</option>
          <option value="en_reparacion">En Reparación</option>
          <option value="reparado">Reparado</option>
          <option value="entregado">Entregado</option>
          <option value="cancelado">Cancelado</option>
        </select>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nº Orden</th>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Equipo</th>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Técnico</th>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Fecha Ingreso</th>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Estado</th>
              <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Prioridad</th>
              <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Costo Total</th>
              <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="orden in filteredOrdenes" :key="orden.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-mono text-sm">{{ orden.numeroOrden }}</td>
              <td class="px-4 py-3">{{ orden.cliente?.razonSocial }}</td>
              <td class="px-4 py-3 text-sm">
                {{ orden.equipo?.marca }} {{ orden.equipo?.modelo }}
              </td>
              <td class="px-4 py-3 text-sm">{{ orden.tecnico?.name }}</td>
              <td class="px-4 py-3 text-sm">
                {{ new Date(orden.fechaIngreso).toLocaleDateString() }}
              </td>
              <td class="px-4 py-3">
                <span :class="`px-2 py-1 rounded-full text-xs font-semibold bg-${estadosColor[orden.estado]}-100 text-${estadosColor[orden.estado]}-700`">
                  {{ orden.estado.replace(/_/g, ' ').toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span :class="`px-2 py-1 rounded-full text-xs font-semibold bg-${prioridadColor[orden.prioridad]}-100 text-${prioridadColor[orden.prioridad]}-700`">
                  {{ orden.prioridad.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3 text-right font-semibold">
                S/ {{ Number(orden.costoTotal).toFixed(2) }}
              </td>
              <td class="px-4 py-3 text-center space-x-2">
                <button @click="handleView(orden)" class="text-blue-600 hover:text-blue-800" title="Ver">
                  👁️
                </button>
                <button @click="handleEdit(orden)" class="text-green-600 hover:text-green-800" title="Editar">
                  ✏️
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
