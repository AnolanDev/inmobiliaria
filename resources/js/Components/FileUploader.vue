<template>
  <div class="file-uploader">
    <div class="mb-4">
      <label :for="id" class="block text-sm font-medium text-gray-700 mb-2">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      
      <div 
        @drop="handleDrop" 
        @dragover.prevent 
        @dragenter.prevent
        :class="[
          'border-2 border-dashed rounded-lg p-6 text-center transition-colors duration-200',
          isDragging ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-gray-400'
        ]"
        @dragenter="isDragging = true"
        @dragleave="isDragging = false"
      >
        <input
          :id="id"
          ref="fileInput"
          type="file"
          :accept="accept"
          :multiple="multiple"
          class="hidden"
          @change="handleFileSelect"
        />
        
        <div class="text-gray-600">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p class="text-lg font-medium">
            {{ dragText }}
          </p>
          <p class="text-sm text-gray-500 mt-2">
            {{ helpText }}
          </p>
          <button
            type="button"
            @click="$refs.fileInput.click()"
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            Seleccionar archivos
          </button>
        </div>
      </div>
    </div>

    <!-- File Previews -->
    <div v-if="previews.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="(preview, index) in previews"
        :key="index"
        class="relative group"
      >
        <!-- Image Preview -->
        <div v-if="preview.type === 'image'" class="aspect-square rounded-lg overflow-hidden bg-gray-100">
          <img
            :src="preview.url"
            :alt="preview.name"
            class="w-full h-full object-cover"
          />
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity duration-200 flex items-center justify-center">
            <button
              @click="removeFile(index)"
              class="opacity-0 group-hover:opacity-100 bg-red-500 text-white rounded-full p-2 hover:bg-red-600 transition-all duration-200"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Video Preview -->
        <div v-else-if="preview.type === 'video'" class="aspect-video rounded-lg overflow-hidden bg-gray-100">
          <video
            :src="preview.url"
            class="w-full h-full object-cover"
            controls
          ></video>
          <div class="absolute top-2 right-2">
            <button
              @click="removeFile(index)"
              class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- File Info -->
        <div class="mt-2">
          <p class="text-xs text-gray-600 truncate" :title="preview.name">
            {{ preview.name }}
          </p>
          <p class="text-xs text-gray-500">
            {{ formatFileSize(preview.size) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Error Messages -->
    <div v-if="errors.length > 0" class="mt-4">
      <div
        v-for="(error, index) in errors"
        :key="index"
        class="text-red-600 text-sm mb-1"
      >
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FileUploader',
  props: {
    id: {
      type: String,
      required: true
    },
    label: {
      type: String,
      default: 'Subir archivos'
    },
    accept: {
      type: String,
      default: 'image/*'
    },
    multiple: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },
    maxSize: {
      type: Number,
      default: 5 * 1024 * 1024 // 5MB
    },
    maxFiles: {
      type: Number,
      default: null
    },
    existingFiles: {
      type: Array,
      default: () => []
    },
    dragText: {
      type: String,
      default: 'Arrastra archivos aquí o haz clic para seleccionar'
    },
    helpText: {
      type: String,
      default: ''
    }
  },
  emits: ['files-changed', 'files-removed'],
  data() {
    return {
      isDragging: false,
      previews: [],
      files: [],
      errors: []
    }
  },
  mounted() {
    try {
      this.loadExistingFiles()
    } catch (error) {
      console.warn('Error loading existing files on mount:', error)
      this.previews = []
    }
  },
  watch: {
    existingFiles: {
      handler() {
        try {
          this.loadExistingFiles()
        } catch (error) {
          console.warn('Error loading existing files:', error)
          this.previews = []
        }
      },
      immediate: true
    }
  },
  methods: {
    handleDrop(e) {
      e.preventDefault()
      this.isDragging = false
      
      const files = Array.from(e.dataTransfer.files)
      this.processFiles(files)
    },
    
    handleFileSelect(e) {
      const files = Array.from(e.target.files)
      this.processFiles(files)
    },
    
    processFiles(newFiles) {
      this.errors = []
      
      // Filter valid files
      const validFiles = newFiles.filter(file => this.validateFile(file))
      
      if (validFiles.length === 0) return
      
      // Check max files limit
      if (this.maxFiles && (this.files.length + validFiles.length) > this.maxFiles) {
        this.errors.push(`Máximo ${this.maxFiles} archivos permitidos`)
        return
      }
      
      // Add files
      validFiles.forEach(file => {
        const preview = this.createPreview(file)
        this.previews.push(preview)
        this.files.push(file)
      })
      
      this.emitFilesChanged()
    },
    
    validateFile(file) {
      // Check file size
      if (file.size > this.maxSize) {
        this.errors.push(`${file.name}: Archivo muy grande (máximo ${this.formatFileSize(this.maxSize)})`)
        return false
      }
      
      // Check file type
      const acceptedTypes = this.accept.split(',').map(type => type.trim())
      const fileType = file.type
      const fileExtension = '.' + file.name.split('.').pop().toLowerCase()
      
      let isValidType = false
      acceptedTypes.forEach(type => {
        if (type === fileType || 
            type === fileExtension || 
            (type.includes('/*') && fileType.startsWith(type.replace('/*', '')))) {
          isValidType = true
        }
      })
      
      if (!isValidType) {
        this.errors.push(`${file.name}: Tipo de archivo no permitido`)
        return false
      }
      
      return true
    },
    
    createPreview(file) {
      const url = URL.createObjectURL(file)
      let type = 'file'
      
      if (file.type.startsWith('image/')) {
        type = 'image'
      } else if (file.type.startsWith('video/')) {
        type = 'video'
      }
      
      return {
        name: file.name,
        size: file.size,
        type,
        url,
        isNew: true
      }
    },
    
    removeFile(index) {
      const preview = this.previews[index]
      
      if (preview.isNew) {
        // Remove from files array
        const fileIndex = this.files.findIndex(f => f.name === preview.name)
        if (fileIndex !== -1) {
          this.files.splice(fileIndex, 1)
        }
        
        // Revoke object URL to free memory
        if (preview.url.startsWith('blob:')) {
          URL.revokeObjectURL(preview.url)
        }
      } else {
        // Emit removal of existing file
        this.$emit('files-removed', [preview.path])
      }
      
      this.previews.splice(index, 1)
      this.emitFilesChanged()
    },
    
    loadExistingFiles() {
      if (!this.existingFiles || this.existingFiles.length === 0) return
      
      this.previews = this.existingFiles.map(file => {
        // Handle different file formats
        let filePath, fileName, fileUrl
        
        if (typeof file === 'string') {
          // file is just a path string
          filePath = file
          fileName = file.split('/').pop()
          fileUrl = this.getFileUrl(file)
        } else if (typeof file === 'object' && file !== null) {
          // file is an object with properties
          filePath = file.path || file.url || ''
          fileName = file.name || (typeof filePath === 'string' ? filePath.split('/').pop() : 'archivo')
          fileUrl = file.url || (filePath ? this.getFileUrl(filePath) : '')
        } else {
          // fallback for unexpected formats
          filePath = ''
          fileName = 'archivo'
          fileUrl = ''
        }
        
        return {
          name: fileName,
          path: filePath,
          url: fileUrl,
          type: this.getFileType(filePath),
          isNew: false,
          size: 0
        }
      })
    },
    
    getFileUrl(path) {
      return `/storage/${path}`
    },
    
    getFileType(path) {
      if (!path || typeof path !== 'string') {
        return 'file'
      }
      
      const extension = path.split('.').pop()?.toLowerCase() || ''
      const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']
      const videoExts = ['mp4', 'webm', 'ogg', 'avi', 'mov']
      
      if (imageExts.includes(extension)) return 'image'
      if (videoExts.includes(extension)) return 'video'
      return 'file'
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    },
    
    emitFilesChanged() {
      this.$emit('files-changed', this.files)
    },
    
    clearFiles() {
      this.files = []
      this.previews.forEach(preview => {
        if (preview.url.startsWith('blob:')) {
          URL.revokeObjectURL(preview.url)
        }
      })
      this.previews = []
      this.errors = []
      this.emitFilesChanged()
    }
  },
  
  beforeUnmount() {
    // Clean up object URLs
    this.previews.forEach(preview => {
      if (preview.url && preview.url.startsWith('blob:')) {
        URL.revokeObjectURL(preview.url)
      }
    })
  }
}
</script>