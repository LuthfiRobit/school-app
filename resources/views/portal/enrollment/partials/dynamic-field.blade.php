@if($field->field_type === 'file')
  <div class="mb-4">
    <label class="form-label fw-bold text-primary-800">
      {{ $field->field_label }}
      @if($field->is_required && empty($formData[$field->id]))
        <span class="text-error">*</span>
      @endif
    </label>
    
    <!-- File Input Container -->
    <div x-data="{ hasFile: {{ !empty($formData[$field->id]) ? 'true' : 'false' }}, fileName: '{{ !empty($formData[$field->id]) ? basename($formData[$field->id]) : '' }}' }">
      
      <!-- Dropzone -->
      <div class="upload-dropzone" x-show="!hasFile">
        <i class="fa-solid fa-cloud-arrow-up fs-3 text-muted mb-2"></i>
        <div class="small fw-semibold text-primary-700">Pilih Berkas atau Seret Kemari</div>
        <span class="text-muted d-block" style="font-size:0.75rem;">
          Maksimal {{ ($field->max_file_size ?? 2048) / 1024 }}MB (MIME: {{ $field->file_types ?? 'pdf,jpg,jpeg,png' }})
        </span>
        <input type="file" 
               name="fields[{{ $field->id }}]" 
               accept="{{ $field->file_types ? '.' . str_replace(',', ',.', $field->file_types) : '.pdf,.jpg,.jpeg,.png' }}"
               @change="handleFileUpload($event, '{{ $field->id }}'); hasFile = true; fileName = $event.target.files[0].name"
               :required="!hasFile && {{ $field->is_required ? 'true' : 'false' }}">
      </div>

      <!-- Preview Card -->
      <div class="file-preview-card" x-show="hasFile" style="display: none;">
        <div class="d-flex align-items-center">
          <div class="rounded bg-primary bg-opacity-10 text-primary-700 d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;font-size:1.4rem;">
            <i class="fa-solid fa-file"></i>
          </div>
          <div>
            <strong class="text-primary-900 small d-block" x-text="fileName"></strong>
            @if(!empty($formData[$field->id]))
              <a href="{{ asset('storage/' . $formData[$field->id]) }}" target="_blank" class="text-xs text-primary-600 text-decoration-none">
                <i class="fa-solid fa-eye me-1"></i> Lihat Berkas Terunggah
              </a>
            @endif
          </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                @click="removeFile('{{ $field->id }}'); hasFile = false; fileName = ''">
          <i class="fa-solid fa-trash-can"></i>
        </button>
      </div>

    </div>
  </div>
@else
  <div class="mb-3">
    <label for="field_{{ $field->id }}" class="form-label text-primary-800">
      {{ $field->field_label }}
      @if($field->is_required)
        <span class="text-error">*</span>
      @endif
    </label>

    @if($field->field_type === 'text')
      <input type="text" 
             id="field_{{ $field->id }}" 
             name="fields[{{ $field->id }}]" 
             class="form-control" 
             placeholder="{{ $field->placeholder ?? 'Masukkan ' . $field->field_label }}"
             x-model="fieldsData['{{ $field->id }}']" 
             @input="triggerAutoSave()"
             {{ $field->is_required ? 'required' : '' }}>

    @elseif($field->field_type === 'number')
      <input type="number" 
             step="any"
             id="field_{{ $field->id }}" 
             name="fields[{{ $field->id }}]" 
             class="form-control" 
             placeholder="{{ $field->placeholder ?? 'Masukkan ' . $field->field_label }}"
             x-model="fieldsData['{{ $field->id }}']" 
             @input="triggerAutoSave()"
             {{ $field->is_required ? 'required' : '' }}>

    @elseif($field->field_type === 'date')
      <input type="date" 
             id="field_{{ $field->id }}" 
             name="fields[{{ $field->id }}]" 
             class="form-control" 
             x-model="fieldsData['{{ $field->id }}']" 
             @change="triggerAutoSave()"
             {{ $field->is_required ? 'required' : '' }}>

    @elseif($field->field_type === 'select')
      <select id="field_{{ $field->id }}" 
              name="fields[{{ $field->id }}]" 
              class="form-select" 
              x-model="fieldsData['{{ $field->id }}']" 
              @change="triggerAutoSave()"
              {{ $field->is_required ? 'required' : '' }}>
        <option value="" disabled selected>Pilih {{ $field->field_label }}</option>
        @if(is_array($field->field_options))
          @foreach($field->field_options as $option)
            <option value="{{ $option }}">{{ $option }}</option>
          @endforeach
        @endif
      </select>

    @elseif($field->field_type === 'textarea')
      <textarea id="field_{{ $field->id }}" 
                name="fields[{{ $field->id }}]" 
                class="form-control" 
                rows="3"
                placeholder="{{ $field->placeholder ?? 'Masukkan ' . $field->field_label }}"
                x-model="fieldsData['{{ $field->id }}']" 
                @input="triggerAutoSave()"
                {{ $field->is_required ? 'required' : '' }}></textarea>
    @endif
  </div>
@endif
