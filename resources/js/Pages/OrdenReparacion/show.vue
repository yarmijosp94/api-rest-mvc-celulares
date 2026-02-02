<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { OrdenReparacion } from '../../types'

const props = defineProps<{
  orden: OrdenReparacion
}>()

const handleEdit = () => {
  router.visit(route('ordenes-reparacion.edit', props.orden.id))
}

const handleBack = () => {
  router.visit(route('ordenes-reparacion.index'))
}
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <div class="mb-6 flex justify-between items-center">
      <button @click="handleBack" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
        ← Volver
      </button>
      <button @click="handleEdit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Editar Orden
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8 space-y-8">
      <div class="border-b pb-6">
        <h1 class="text-3xl font-bold">Orden: {{ orden.numeroOrden }}</h1>
        <div class="mt-4 flex gap-4">
          <span :class="`px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700`">
            {{ orden.estado.replace(/_/g, ' ').toUpperCase() }}
          </span>
          <span :class="`px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-700`">
            Prioridad: {{ orden.prioridad.toUpperCase() }}
          </span>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div>
          <h2 class="text-xl font-semibold mb-4">Información del Cliente</h2>
          <div class="space-y-2 text-gray-700">
            <p><span class="font-medium">Razón Social:</span> {{ orden.cliente?.razonSocial }}</p>
            <p><span class="font-medium">Documento:</span> {{ orden.cliente?.numeroDocumento }}</p>
            <p><span class="font-medium">Teléfono:</span> {{ orden.cliente?.telefono }}</p>
          </div>
        </div>

        <div>
          <h2 class="text-xl font-semibold mb-4">Información del Equipo</h2>
          <div class="space-y-2 text-gray-700">
            <p><span class="font-medium">Marca:</span> {{ orden.equipo?.marca }}</p>
            <p><span class="font-medium">Modelo:</span> {{ orden.equipo?.modelo }}</p>
            <p><span class="font-medium">IMEI:</span> {{ orden.equipo?.imei }}</p>
            <p><span class="font-medium">Color:</span> {{ orden.equipo?.color }}</p>
          </div>
        </div>
      </div>

      <div>
        <h2 class="text-xl font-semibold mb-4">Detalles de la Reparación</h2>
        <div class="space-y-4">
          <div>
            <p class="font-medium text-gray-700">Falla Reportada:</p>
            <p class="mt-1 text-gray-600">{{ orden.fallaReportada }}</p>
          </div>

          <div v-if="orden.diagnosticoTecnico">
            <p class="font-medium text-gray-700">Diagnóstico Técnico:</p>
            <p class="mt-1 text-gray-600">{{ orden.diagnosticoTecnico }}</p>
          </div>

          <div v-if="orden.solucionAplicada">
            <p class="font-medium text-gray-700">Solución Aplicada:</p>
            <p class="mt-1 text-gray-600">{{ orden.solucionAplicada }}</p>
          </div>
        </div>
      </div>

      <div v-if="orden.repuestos && orden.repuestos.length > 0">
        <h2 class="text-xl font-semibold mb-4">Repuestos Utilizados</h2>
        <table class="w-full border">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Código</th>
              <th class="px-4 py-2 text-left">Nombre</th>
              <th class="px-4 py-2 text-right">Cantidad</th>
              <th class="px-4 py-2 text-right">Precio Unit.</th>
              <th class="px-4 py-2 text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="repuesto in orden.repuestos" :key="repuesto.id" class="border-t">
              <td class="px-4 py-2 font-mono">{{ repuesto.codigo }}</td>
              <td class="px-4 py-2">{{ repuesto.nombre }}</td>
              <td class="px-4 py-2 text-right">{{ repuesto.pivot.cantidad }}</td>
              <td class="px-4 py-2 text-right">S/ {{ repuesto.pivot.precioUnitario.toFixed(2) }}</td>
              <td class="px-4 py-2 text-right font-semibold">S/ {{ repuesto.pivot.subtotal.toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="grid md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg">
        <div>
          <p class="text-gray-600">Costo Mano de Obra</p>
          <p class="text-2xl font-bold">S/ {{ Number(orden.costoManoObra).toFixed(2) }}</p>
        </div>
        <div>
          <p class="text-gray-600">Costo Repuestos</p>
          <p class="text-2xl font-bold">S/ {{ Number(orden.costoRepuestos).toFixed(2) }}</p>
        </div>
        <div>
          <p class="text-gray-600">Costo Total</p>
          <p class="text-3xl font-bold text-blue-600">S/ {{ Number(orden.costoTotal).toFixed(2) }}</p>
        </div>
      </div>

      <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-600">
        <div>
          <p><span class="font-medium">Técnico:</span> {{ orden.tecnico?.name }}</p>
          <p><span class="font-medium">Fecha Ingreso:</span> {{ new Date(orden.fechaIngreso).toLocaleDateString() }}</p>
          <p v-if="orden.fechaPromesa"><span class="font-medium">Fecha Promesa:</span> {{ new Date(orden.fechaPromesa).toLocaleDateString() }}</p>
        </div>
        <div>
          <p v-if="orden.adelanto"><span class="font-medium">Adelanto:</span> S/ {{ Number(orden.adelanto).toFixed(2) }}</p>
          <p v-if="orden.fechaEntrega"><span class="font-medium">Fecha Entrega:</span> {{ new Date(orden.fechaEntrega).toLocaleDateString() }}</p>
          <p v-if="orden.observaciones"><span class="font-medium">Observaciones:</span> {{ orden.observaciones }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
