<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { OrdenReparacion, Cliente, Repuesto } from '../../types'

const props = defineProps<{
  orden: OrdenReparacion
  clientes: Array<{ id: string; razonSocial: string }>
  equipos: Array<{ id: string; marca: string; modelo: string; imei: string }>
  tecnicos: Array<{ id: string; name: string }>
  repuestos: Repuesto[]
  estados: Record<string, string>
}>()

const tabActivo = ref('info')

const form = useForm({
  tecnico_id: props.orden.tecnicoId,
  fecha_promesa: props.orden.fechaPromesa ? props.orden.fechaPromesa.split(' ')[0] : '',
  diagnostico_tecnico: props.orden.diagnosticoTecnico || '',
  solucion_aplicada: props.orden.solucionAplicada || '',
  prioridad: props.orden.prioridad,
  costo_mano_obra: props.orden.costoManoObra,
  adelanto: props.orden.adelanto || 0,
  observaciones: props.orden.observaciones || ''
})

const repuestoForm = useForm({
  repuesto_id: '',
  cantidad: 1
})

const submit = () => {
  form.put(route('ordenes-reparacion.update', props.orden.id), {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('ordenes-reparacion.index'))
    }
  })
}

const agregarRepuesto = () => {
  repuestoForm.post(route('ordenes-reparacion.asignar-repuesto', props.orden.id), {
    preserveScroll: true,
    onSuccess: () => {
      repuestoForm.reset()
      router.reload({ only: ['orden'] })
    }
  })
}

const quitarRepuesto = (repuestoId: string) => {
  router.delete(route('ordenes-reparacion.quitar-repuesto', { ordenId: props.orden.id, repuestoId }), {
    preserveScroll: true
  })
}

const handleBack = () => {
  router.visit(route('ordenes-reparacion.index'))
}
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <div class="mb-6">
      <button @click="handleBack" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
        ← Volver
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
      <h1 class="text-3xl font-bold mb-2">Editar Orden: {{ orden.numeroOrden }}</h1>
      <p class="text-gray-600 mb-6">Estado actual: <span class="font-semibold">{{ orden.estado }}</span></p>

      <div class="border-b mb-6">
        <nav class="flex space-x-8">
          <button @click="tabActivo = 'info'" 
            :class="['pb-4 px-1 border-b-2 font-medium', tabActivo === 'info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700']">
            Información General
          </button>
          <button @click="tabActivo = 'repuestos'"
            :class="['pb-4 px-1 border-b-2 font-medium', tabActivo === 'repuestos' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700']">
            Repuestos
          </button>
        </nav>
      </div>

      <div v-if="tabActivo === 'info'" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
          <div>
            <p class="text-sm text-gray-600">Cliente</p>
            <p class="font-semibold">{{ orden.cliente?.razonSocial }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Equipo</p>
            <p class="font-semibold">{{ orden.equipo?.marca }} {{ orden.equipo?.modelo }}</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Falla Reportada (readonly)</label>
          <textarea readonly :value="orden.fallaReportada" rows="3" class="w-full px-4 py-2 border rounded-lg bg-gray-50"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico Técnico</label>
          <textarea v-model="form.diagnostico_tecnico" rows="4" class="w-full px-4 py-2 border rounded-lg"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Solución Aplicada</label>
          <textarea v-model="form.solucion_aplicada" rows="4" class="w-full px-4 py-2 border rounded-lg"></textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Técnico Asignado</label>
            <select v-model="form.tecnico_id" class="w-full px-4 py-2 border rounded-lg">
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

        <div class="grid md:grid-cols-2 gap-4">
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
          <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Promesa de Entrega</label>
          <input type="date" v-model="form.fecha_promesa" class="w-full px-4 py-2 border rounded-lg" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
          <textarea v-model="form.observaciones" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
        </div>

        <div class="flex justify-end">
          <button type="button" @click="submit" :disabled="form.processing"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-300">
            Guardar Cambios
          </button>
        </div>
      </div>

      <div v-if="tabActivo === 'repuestos'" class="space-y-6">
        <div class="bg-blue-50 p-4 rounded-lg">
          <h3 class="font-semibold mb-4">Agregar Repuesto</h3>
          <div class="grid md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <select v-model="repuestoForm.repuesto_id" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Seleccione un repuesto</option>
                <option v-for="repuesto in repuestos" :key="repuesto.id" :value="repuesto.id">
                  {{ repuesto.codigo }} - {{ repuesto.nombre }} (Stock: {{ repuesto.stockActual }})
                </option>
              </select>
            </div>
            <div class="flex gap-2">
              <input type="number" v-model.number="repuestoForm.cantidad" min="1" 
                placeholder="Cant." class="w-20 px-4 py-2 border rounded-lg" />
              <button @click="agregarRepuesto" :disabled="!repuestoForm.repuesto_id || repuestoForm.processing"
                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-300">
                Agregar
              </button>
            </div>
          </div>
        </div>

        <div v-if="orden.repuestos && orden.repuestos.length > 0">
          <h3 class="font-semibold mb-4">Repuestos Asignados</h3>
          <table class="w-full border rounded-lg">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left">Código</th>
                <th class="px-4 py-3 text-left">Nombre</th>
                <th class="px-4 py-3 text-right">Cantidad</th>
                <th class="px-4 py-3 text-right">Precio Unit.</th>
                <th class="px-4 py-3 text-right">Subtotal</th>
                <th class="px-4 py-3 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="repuesto in orden.repuestos" :key="repuesto.id" class="border-t">
                <td class="px-4 py-3 font-mono">{{ repuesto.codigo }}</td>
                <td class="px-4 py-3">{{ repuesto.nombre }}</td>
                <td class="px-4 py-3 text-right">{{ repuesto.pivot.cantidad }}</td>
                <td class="px-4 py-3 text-right">S/ {{ repuesto.pivot.precioUnitario.toFixed(2) }}</td>
                <td class="px-4 py-3 text-right font-semibold">S/ {{ repuesto.pivot.subtotal.toFixed(2) }}</td>
                <td class="px-4 py-3 text-center">
                  <button @click="quitarRepuesto(repuesto.id)" class="text-red-600 hover:text-red-800" title="Eliminar">
                    🗑️
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
              <tr>
                <td colspan="4" class="px-4 py-3 text-right">Total Repuestos:</td>
                <td class="px-4 py-3 text-right">S/ {{ Number(orden.costoRepuestos).toFixed(2) }}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg">
          <div class="grid md:grid-cols-3 gap-4">
            <div>
              <p class="text-gray-600">Costo Mano de Obra</p>
              <p class="text-xl font-bold">S/ {{ Number(orden.costoManoObra).toFixed(2) }}</p>
            </div>
            <div>
              <p class="text-gray-600">Costo Repuestos</p>
              <p class="text-xl font-bold">S/ {{ Number(orden.costoRepuestos).toFixed(2) }}</p>
            </div>
            <div>
              <p class="text-gray-600">Costo Total</p>
              <p class="text-2xl font-bold text-blue-600">S/ {{ Number(orden.costoTotal).toFixed(2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
