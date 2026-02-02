<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Cliente, Equipo } from '../../types'

const props = defineProps<{
  clientes: Cliente[]
  equipos: Equipo[]
  tecnicos: Array<{ id: string; name: string }>
  repuestos: Equipo[]
}>()

const paso = ref(1)
const equiposDelCliente = ref<Equipo[]>([])

const form = useForm({
  cliente_id: '',
  equipo_id: '',
  tecnico_id: '',
  fecha_ingreso: new Date().toISOString().split('T')[0],
  fecha_promesa: '',
  falla_reportada: '',
  prioridad: 'media',
  costo_mano_obra: 0,
  adelanto: 0,
  observaciones: '',
  requiere_aprobacion: true,
  estado: 'recibido'
})

watch(() => form.cliente_id, async (clienteId) => {
  if (clienteId) {
    const response = await fetch(`/clientes/${clienteId}/equipos`)
    const data = await response.json()
    equiposDelCliente.value = data.equipos
    form.equipo_id = ''
  }
})

const siguiente = () => {
  if (paso.value === 1 && form.cliente_id && form.equipo_id) {
    paso.value = 2
  }
}

const anterior = () => {
  if (paso.value > 1) {
    paso.value--
  }
}

const submit = () => {
  form.post(route('ordenes-reparacion.store'), {
    onSuccess: () => {
      router.visit(route('ordenes-reparacion.index'))
    }
  })
}
</script>

<template>
  <div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
      <h1 class="text-3xl font-bold mb-6">Nueva Orden de Reparación</h1>

      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div :class="['flex-1 text-center', paso >= 1 ? 'text-blue-600 font-semibold' : 'text-gray-400']">
            Paso 1: Cliente y Equipo
          </div>
          <div :class="['flex-1 text-center', paso >= 2 ? 'text-blue-600 font-semibold' : 'text-gray-400']">
            Paso 2: Detalles de la Reparación
          </div>
        </div>
        <div class="mt-2 h-2 bg-gray-200 rounded-full">
          <div :class="['h-full bg-blue-600 rounded-full transition-all']" :style="{ width: `${(paso / 2) * 100}%` }"></div>
        </div>
      </div>

      <div v-if="paso === 1" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
          <select v-model="form.cliente_id" class="w-full px-4 py-2 border rounded-lg" required>
            <option value="">Seleccione un cliente</option>
            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
              {{ cliente.razonSocial }} - {{ cliente.numeroDocumento }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Equipo *</label>
          <select v-model="form.equipo_id" :disabled="!form.cliente_id" class="w-full px-4 py-2 border rounded-lg" required>
            <option value="">Seleccione un equipo</option>
            <option v-for="equipo in equiposDelCliente" :key="equipo.id" :value="equipo.id">
              {{ equipo.marca }} {{ equipo.modelo }} - IMEI: {{ equipo.imei }}
            </option>
          </select>
          <p v-if="form.cliente_id && equiposDelCliente.length === 0" class="mt-2 text-sm text-yellow-600">
            Este cliente no tiene equipos registrados. Debe crear uno primero.
          </p>
        </div>

        <div class="flex justify-end space-x-4">
          <button type="button" @click="siguiente" :disabled="!form.cliente_id || !form.equipo_id"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300">
            Siguiente
          </button>
        </div>
      </div>

      <div v-if="paso === 2" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Falla Reportada *</label>
          <textarea v-model="form.falla_reportada" rows="4" class="w-full px-4 py-2 border rounded-lg" 
            placeholder="Describa el problema reportado por el cliente" required></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Técnico Asignado *</label>
            <select v-model="form.tecnico_id" class="w-full px-4 py-2 border rounded-lg" required>
              <option value="">Seleccione un técnico</option>
              <option v-for="tecnico in tecnicos" :key="tecnico.id" :value="tecnico.id">
                {{ tecnico.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
            <select v-model="form.prioridad" class="w-full px-4 py-2 border rounded-lg">
              <option value="baja">Baja</option>
              <option value="media">Media</option>
              <option value="alta">Alta</option>
              <option value="urgente">Urgente</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso</label>
            <input type="date" v-model="form.fecha_ingreso" class="w-full px-4 py-2 border rounded-lg" required />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Promesa de Entrega</label>
            <input type="date" v-model="form.fecha_promesa" class="w-full px-4 py-2 border rounded-lg" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Costo Mano de Obra (S/)</label>
            <input type="number" v-model.number="form.costo_mano_obra" step="0.01" class="w-full px-4 py-2 border rounded-lg" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Adelanto (S/)</label>
            <input type="number" v-model.number="form.adelanto" step="0.01" class="w-full px-4 py-2 border rounded-lg" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
          <textarea v-model="form.observaciones" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
        </div>

        <div class="flex items-center">
          <input type="checkbox" v-model="form.requiere_aprobacion" class="mr-2" />
          <label class="text-sm text-gray-700">Requiere aprobación del cliente</label>
        </div>

        <div class="flex justify-between">
          <button type="button" @click="anterior" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Anterior
          </button>
          <button type="button" @click="submit" :disabled="form.processing" 
            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-300">
            Crear Orden
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
