<script setup lang="ts">
import { ref, computed, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'
import type { Repuesto } from '../../types'

const UAvatar = resolveComponent('UAvatar')
const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const UModal = resolveComponent('UModal')
const UBadge = resolveComponent('UBadge')
const UAlert = resolveComponent('UAlert')

const props = defineProps<{
  repuestos: {
    data: Repuesto[]
    links: any[]
    meta: any
  }
  stats?: {
    total_repuestos: number
    stock_bajo: number
    valor_total_inventario: string
  }
}>()

const toast = useToast()
const table = useTemplateRef('table')

const columnFilters = ref<{ id: string; value: string }[]>([])
const globalFilter = ref('')
const columnVisibility = ref()
const rowSelection = ref({})

const status = ref<'idle' | 'pending' | 'success' | 'error'>('success')
const isDeleteModalOpen = ref(false)
const repuestoToDelete = ref<Repuesto | null>(null)

const handleCreate = () => {
  router.visit(route('repuestos.create'))
}

const handleEdit = (repuesto: Repuesto) => {
  router.visit(route('repuestos.edit', repuesto.id))
}

const confirmDelete = (repuesto: Repuesto) => {
  repuestoToDelete.value = repuesto
  isDeleteModalOpen.value = true
}

const handleDelete = () => {
  if (!repuestoToDelete.value) return

  router.delete(route('repuestos.destroy', repuestoToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDeleteModalOpen.value = false
      repuestoToDelete.value = null
    }
  })
}

function getRowItems(row: Row<Repuesto>) {
  return [
    {
      type: 'label',
      label: 'Acciones'
    },
    {
      label: 'Copiar ID de repuesto',
      icon: 'i-lucide-copy',
      onSelect() {
        navigator.clipboard.writeText(row.original.id.toString())
        toast.add({
          title: 'Copiado al portapapeles',
          description: 'ID del repuesto copiado'
        })
      }
    },
    {
      type: 'separator'
    },
    {
      label: 'Editar repuesto',
      icon: 'i-lucide-pencil',
      onSelect() {
        handleEdit(row.original)
      }
    },
    {
      type: 'separator'
    },
    {
      label: 'Eliminar repuesto',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        confirmDelete(row.original)
      }
    }
  ]
}

const getEstadoColor = (estado: string) => {
  switch (estado) {
    case 'disponible':
      return 'success'
    case 'por_pedir':
      return 'warning'
    case 'agotado':
      return 'error'
    default:
      return 'neutral'
  }
}

const getCategoriaColor = (categoria: string) => {
  const colors: Record<string, string> = {
    'Pantalla': 'primary',
    'Batería': 'warning',
    'Cámara': 'info',
    'Placa': 'error',
    'Conector': 'success',
    'Botón': 'neutral',
    'Altavoz': 'purple',
    'Otros': 'neutral'
  }
  return colors[categoria] || 'neutral'
}

const getStockColor = (repuesto: Repuesto) => {
  if (repuesto.stockActual < repuesto.stockMinimo) {
    return 'error'
  } else if (repuesto.stockActual === repuesto.stockMinimo) {
    return 'warning'
  }
  return 'success'
}

const stockBajoAlert = computed(() => {
  return props.stats && props.stats.stock_bajo > 0
})

const columns: TableColumn<Repuesto>[] = [
  {
    id: 'select',
    header: ({ table }) =>
      h(UCheckbox, {
        'modelValue': table.getIsSomePageRowsSelected()
          ? 'indeterminate'
          : table.getIsAllPageRowsSelected(),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') =>
          table.toggleAllPageRowsSelected(!!value),
        'ariaLabel': 'Seleccionar todos'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        'modelValue': row.getIsSelected(),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => row.toggleSelected(!!value),
        'ariaLabel': 'Seleccionar fila'
      })
  },
  {
    accessorKey: 'codigo',
    header: 'Código',
    cell: ({ row }) => {
      return h('div', { class: 'flex items-center gap-2' }, [
        h('span', { class: 'font-medium text-highlighted' }, row.original.codigo)
      ])
    }
  },
  {
    accessorKey: 'nombre',
    header: 'Nombre / Descripción',
    cell: ({ row }) => {
      return h('div', { class: 'flex flex-col' }, [
        h('p', { class: 'font-medium text-highlighted' }, row.original.nombre),
        row.original.descripcion
          ? h('p', { class: 'text-sm text-muted truncate max-w-xs' }, row.original.descripcion)
          : null
      ])
    }
  },
  {
    accessorKey: 'categoria',
    header: 'Categoría',
    cell: ({ row }) => {
      return h(UBadge, {
        color: getCategoriaColor(row.original.categoria),
        label: row.original.categoria
      })
    }
  },
  {
    accessorKey: 'marcaCompatible',
    header: 'Compatible',
    cell: ({ row }) => {
      const marca = row.original.marcaCompatible
      const modelo = row.original.modeloCompatible
      if (!marca && !modelo) return h('span', { class: 'text-muted' }, '-')
      return h('div', { class: 'text-sm' }, [
        marca ? h('p', { class: 'font-medium' }, marca) : null,
        modelo ? h('p', { class: 'text-muted' }, modelo) : null
      ])
    }
  },
  {
    accessorKey: 'stockActual',
    header: 'Stock',
    cell: ({ row }) => {
      return h(UBadge, {
        color: getStockColor(row.original),
        label: `${row.original.stockActual} / ${row.original.stockMinimo}`
      })
    }
  },
  {
    accessorKey: 'precioVenta',
    header: 'Precio Venta',
    cell: ({ row }) => {
      return h('span', { class: 'font-medium' }, `$${row.original.precioVenta.toFixed(2)}`)
    }
  },
  {
    accessorKey: 'estado',
    header: 'Estado',
    cell: ({ row }) => {
      const labels: Record<string, string> = {
        'disponible': 'Disponible',
        'agotado': 'Agotado',
        'por_pedir': 'Por Pedir'
      }
      return h(UBadge, {
        color: getEstadoColor(row.original.estado),
        label: labels[row.original.estado]
      })
    }
  },
  {
    id: 'actions',
    cell: ({ row }) => {
      return h(
        'div',
        { class: 'text-right' },
        h(
          UDropdownMenu,
          {
            content: {
              align: 'end'
            },
            items: getRowItems(row)
          },
          () =>
            h(UButton, {
              icon: 'i-lucide-ellipsis-vertical',
              color: 'neutral',
              variant: 'ghost',
              class: 'ml-auto'
            })
        )
      )
    }
  }
]

const search = computed({
  get: (): string => {
    return globalFilter.value
  },
  set: (value: string) => {
    globalFilter.value = value
  }
})

const pagination = ref({
  pageIndex: 0,
  pageSize: 10
})
</script>

<template>
  <UDashboardPanel id="repuestos">
    <template #header>
      <UDashboardNavbar title="Repuestos">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            label="Nuevo Repuesto"
            color="primary"
            icon="i-lucide-plus"
            @click="handleCreate"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UAlert
        v-if="stockBajoAlert"
        color="warning"
        icon="i-lucide-alert-triangle"
        :title="`Atención: ${stats?.stock_bajo} repuesto(s) con stock bajo`"
        description="Algunos repuestos tienen stock por debajo del mínimo. Considera hacer un pedido."
        class="mb-4"
      />

      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          v-model="search"
          class="w-full sm:w-1/2"
          size="xl"
          icon="i-lucide-search"
          placeholder="Buscar repuesto por código, nombre o categoría..."
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <UButton
            v-if="table?.tableApi?.getFilteredSelectedRowModel().rows.length"
            label="Eliminar"
            color="error"
            variant="subtle"
            icon="i-lucide-trash"
          >
            <template #trailing>
              <UKbd>
                {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length }}
              </UKbd>
            </template>
          </UButton>

          <UDropdownMenu
            :items="
              table?.tableApi
                ?.getAllColumns()
                .filter((column: any) => column.getCanHide())
                .map((column: any) => ({
                  label: upperFirst(column.id),
                  type: 'checkbox' as const,
                  checked: column.getIsVisible(),
                  onUpdateChecked(checked: boolean) {
                    table?.tableApi?.getColumn(column.id)?.toggleVisibility(!!checked)
                  },
                  onSelect(e?: Event) {
                    e?.preventDefault()
                  }
                }))
            "
            :content="{ align: 'end' }"
          >
            <UButton
              label="Mostrar"
              color="neutral"
              variant="outline"
              trailing-icon="i-lucide-settings-2"
            />
          </UDropdownMenu>
        </div>
      </div>

      <UTable
        ref="table"
        v-model:column-filters="columnFilters"
        v-model:column-visibility="columnVisibility"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{
          getPaginationRowModel: getPaginationRowModel()
        }"
        :filter-options="{
          getFilteredRowModel: getFilteredRowModel()
        }"
        class="shrink-0"
        :data="repuestos.data"
        :columns="columns"
        :loading="status === 'pending'"
        :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2 first:rounded-l-lg last:rounded-r-lg border-y border-default first:border-l last:border-r',
          td: 'border-b border-default',
          separator: 'h-0'
        }"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">
          {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length || 0 }} de
          {{ table?.tableApi?.getFilteredRowModel().rows.length || 0 }} fila(s) seleccionada(s).
        </div>

        <div class="flex items-center gap-1.5">
          <UPagination
            :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
            :items-per-page="table?.tableApi?.getState().pagination.pageSize"
            :total="table?.tableApi?.getFilteredRowModel().rows.length"
            @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
          />
        </div>
      </div>
    </template>
  </UDashboardPanel>

  <UModal
    v-model:open="isDeleteModalOpen"
    title="Confirmar eliminación"
    :description="`¿Está seguro de que desea eliminar el repuesto ${repuestoToDelete?.nombre}? Esta acción no se puede deshacer.`"
  >
    <template #footer>
      <div class="flex justify-end gap-3">
        <UButton
          label="Eliminar"
          color="primary"
          variant="solid"
          icon="i-lucide-trash"
          @click="handleDelete"
        />
        <UButton
          label="Cancelar"
          color="neutral"
          variant="subtle"
          @click="isDeleteModalOpen = false"
        />
      </div>
    </template>
  </UModal>
</template>
