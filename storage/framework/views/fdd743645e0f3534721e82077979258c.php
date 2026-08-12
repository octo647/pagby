<div>
    
    <h1>Escolha um ou mais serviços:</h1>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <br>
        
        <table class="table-auto w-full text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr><th>Serviço</th><th>Preço</th><th>Tempo(minutos)</th><th>
                
            </th></tr>
            </thead>
            <form wire:submit="selectedServices">
            <?php echo csrf_field(); ?>
            <?php $__currentLoopData = $salon_serv; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                   
            <tr wire:key="<?php echo e($index); ?>" class="bg-white border-b hover:bg-gray-50">
                
                <td>                
                    <?php echo e($service['service']); ?>

                </td>
                
                <td >
                    <?php echo e($service['price']); ?>

                </td>
                <td>
                    <?php echo e($service['time']); ?>

                </td>
                
                <td>
                    <input type='checkbox' name = "<?php echo e($index); ?>" wire:model.defer="selected_serv" value="<?php echo e($index); ?>">
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       
            <tr>
                <th></th><th></th><th></th><th>
                    <button type="submit" class="btn btn-primary">
                        Prosseguir
                    </button> 
                </th>
            </tr>
            </form>     

                    
            
            
            
           
            
        </table>
        
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/avail-services.blade.php ENDPATH**/ ?>