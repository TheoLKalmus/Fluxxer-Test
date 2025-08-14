<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Fluxxer</h1>
        <p class="text-lg text-gray-600">Gerenciador de Tasks com Filas Redis</p>
      </div>

      <!-- Task Form -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex gap-4">
          <input 
            v-model="newTaskTitle" 
            @keyup.enter="createTask"
            placeholder="Digite o título da task..."
            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :disabled="isCreating"
          />
          <button 
            @click="createTask" 
            :disabled="!newTaskTitle.trim() || isCreating"
            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="isCreating">Criando...</span>
            <span v-else>Criar Task</span>
          </button>
        </div>
      </div>

      <!-- Tasks List -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Lista de Tasks</h2>
        
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-600">Carregando tasks...</p>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="tasks.length === 0" class="text-center py-12">
          <div class="text-gray-400 mb-4">
            <div class="mx-auto w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
              <span class="text-2xl">📋</span>
            </div>
          </div>
          <p class="text-gray-600 text-lg mb-2">Nenhuma task encontrada</p>
          <p class="text-sm text-gray-500">Crie sua primeira task acima para começar!</p>
        </div>
        
        <!-- Tasks Grid -->
        <div v-else class="space-y-4">
          <div 
            v-for="task in tasks" 
            :key="task.id" 
            class="bg-white border-l-4 rounded-lg p-4 transition-all duration-200 hover:shadow-md"
            :class="{
              'border-yellow-400': task.status === 'pending' || task.status === 'processing',
              'border-green-400': task.status === 'done'
            }"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ task.title }}</h3>
                
                <!-- Status Badge -->
                <div class="mb-3">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        :class="getStatusBadgeClasses(task.status)">
                    {{ getStatusLabel(task.status) }}
                  </span>
                </div>
                
                <!-- Datas com quebras de linha -->
                <div class="space-y-2 text-sm text-gray-600">
                  <div class="flex items-center gap-2">
                    <span class="text-gray-400">📅</span>
                    <span>Criada em: {{ formatDate(task.created_at) }}</span>
                  </div>
                  
                  <div v-if="task.updated_at !== task.created_at" class="flex items-center gap-2">
                    <span class="text-gray-400">🔄</span>
                    <span>Atualizada em: {{ formatDate(task.updated_at) }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Status Icon -->
              <div class="ml-4 flex-shrink-0">
                <div v-if="task.status === 'pending'" class="text-yellow-500">
                  <span class="text-lg">⏰</span>
                </div>
                <div v-else-if="task.status === 'processing'" class="text-yellow-500">
                  <span class="text-lg">⚡</span>
                </div>
                <div v-else-if="task.status === 'done'" class="text-green-500">
                  <span class="text-lg">✅</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Auto-refresh Info -->
      <div class="text-center mt-8 text-sm text-gray-500">
        <p>As tasks são atualizadas automaticamente quando necessário</p>
        <p>Última atualização: {{ lastUpdate }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TaskList',
  data() {
    return {
      tasks: [],
      newTaskTitle: '',
      loading: true,
      isCreating: false,
      lastUpdate: 'Nunca',
      refreshInterval: null
    }
  },
  mounted() {
    this.loadTasks()
    // Auto-refresh a cada 10 segundos APENAS se houver tasks processing
    this.refreshInterval = setInterval(() => {
      // Só atualiza se houver tasks E se houver tasks em processamento
      if (this.tasks.length > 0 && this.tasks.some(task => task.status === 'processing')) {
        this.loadTasks()
      }
    }, 10000)
  },
  beforeUnmount() {
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval)
    }
  },
  methods: {
    async loadTasks() {
      try {
        const response = await fetch('/api/tasks')
        if (response.ok) {
          this.tasks = await response.json()
          this.lastUpdate = new Date().toLocaleTimeString('pt-BR')
        }
      } catch (error) {
        console.error('Erro ao carregar tasks:', error)
      } finally {
        this.loading = false
      }
    },
    
    async createTask() {
      if (!this.newTaskTitle.trim() || this.isCreating) return
      
      this.isCreating = true
      try {
        const response = await fetch('/api/tasks', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ title: this.newTaskTitle.trim() })
        })
        
        if (response.ok) {
          const newTask = await response.json()
          this.newTaskTitle = ''
          // Adiciona a nova task diretamente ao array em vez de recarregar tudo
          this.tasks.unshift(newTask)
          this.lastUpdate = new Date().toLocaleTimeString('pt-BR')
        }
      } catch (error) {
        console.error('Erro ao criar task:', error)
      } finally {
        this.isCreating = false
      }
    },
    
    getStatusBadgeClasses(status) {
      switch (status) {
        case 'pending':
          return 'bg-yellow-100 text-yellow-800'
        case 'processing':
          return 'bg-yellow-100 text-yellow-800'
        case 'done':
          return 'bg-green-100 text-green-800'
        default:
          return 'bg-gray-100 text-gray-800'
      }
    },
    
    getStatusLabel(status) {
      const labels = {
        pending: 'Pendente',
        processing: 'Processando',
        done: 'Concluída'
      }
      return labels[status] || status
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleString('pt-BR')
    }
  }
}
</script>

<style scoped>
/* Estilos específicos do componente */
.task-list {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: Arial, sans-serif;
}

/* Animações suaves */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.task-item {
  animation: fadeIn 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 640px) {
  .task-form {
    flex-direction: column;
    gap: 1rem;
  }
  
  .task-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
}
</style> 