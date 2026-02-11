<script setup lang="ts">
import { ref, computed, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const UModal = resolveComponent('UModal')
const UBadge = resolveComponent('UBadge')
const UAlert = resolveComponent('UAlert')

interface Producto {
  id: string
  categoriaId: string
  codigo: string
  nombre: string
  descripcion?: string
  precioUnitario: number
  stock: number
  tipo: string
  activo: boolean
  createdAt: string
  updatedAt: string
}

const props = defineProps<{
  productos: {
    data: Producto[]
    links: any[]
    meta: any
  }
  stats?: {
    total_productos: number
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
const productoToDelete = ref<Producto | null>(null)

const handleCreate = () => {
  router.visit(route('productos.create'))
}

const handleEdit = (producto: Producto) => {
  router.visit(route('productos.edit', producto.id))
}

const confirmDelete = (producto: Producto) => {
  productoToDelete.value = producto
  isDeleteModalOpen.value = true
}

const handleDelete = () => {
  if (!productoToDelete.value) return

  router.delete(route('productos.destroy', productoToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDeleteModalOpen.value = false
      productoToDelete.value = null
    }
  })
}

function getRowItems(row: Row<Producto>) {
  return [
    {
      type: 'label',
      label: 'Acciones'
    },
    {
      label: 'Copiar ID de producto',
      icon: 'i-lucide-copy',
      onSelect() {
        navigator.clipboard.writeText(row.original.id.toString())
        toast.add({
          title: 'Copiado al portapapeles',
          description: 'ID del producto copiado'
        })
      }
    },
    {
      type: 'separator'
    },
    {
      label: 'Editar producto',
      icon: 'i-lucide-pencil',
      onSelect() {
        handleEdit(row.original)
      }
    },
    {
      type: 'separator'
    },
    {
      label: 'Eliminar producto',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        confirmDelete(row.original)
      }
    }
  ]
}

const getTipoColor = (tipo: string) => {
  return tipo === 'bien' ? 'primary' : 'info'
}

const getTipoLabel = (tipo: string) => {
  return tipo === 'bien' ? 'Bien' : 'Servicio'
}

const getStockColor = (stock: number) => {
  if (stock < 10) {
    return 'error'
  } else if (stock < 20) {
    return 'warning'
  }
  return 'success'
}

const stockBajoAlert = computed(() => {
  return props.stats && props.stats.stock_bajo > 0
})

const columns: TableColumn<Producto>[] = [
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
    accessorKey: 'tipo',
    header: 'Tipo',
    cell: ({ row }) => {
      return h(UBadge, {
        color: getTipoColor(row.original.tipo),
        label: getTipoLabel(row.original.tipo)
      })
    }
  },
  {
    accessorKey: 'stock',
    header: 'Stock',
    cell: ({ row }) => {
      return h(UBadge, {
        color: getStockColor(row.original.stock),
        label: row.original.stock.toString()
      })
    }
  },
  {
    accessorKey: 'precioUnitario',
    header: 'Precio Unitario',
    cell: ({ row }) => {
      return h('span', { class: 'font-medium' }, `$${row.original.precioUnitario.toFixed(2)}`)
    }
  },
  {
    accessorKey: 'activo',
    header: 'Estado',
    cell: ({ row }) => {
      return h(UBadge, {
        color: row.original.activo ? 'success' : 'neutral',
        label: row.original.activo ? 'Activo' : 'Inactivo'
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
  <UDashboardPanel id="productos">
    <template #header>
      <UDashboardNavbar title="Productos">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            label="Nuevo Producto"
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
        :title="`Atención: ${stats?.stock_bajo} producto(s) con stock bajo`"
        description="Algunos productos tienen stock por debajo de 10 unidades. Considera reponer inventario."
        class="mb-4"
      />

      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          v-model="search"
          class="w-full sm:w-1/2"
          size="xl"
          icon="i-lucide-search"
          placeholder="Buscar producto por código, nombre o tipo..."
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
        :data="productos.data"
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
    :description="`¿Está seguro de que desea eliminar el producto ${productoToDelete?.nombre}? Esta acción no se puede deshacer.`"
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
