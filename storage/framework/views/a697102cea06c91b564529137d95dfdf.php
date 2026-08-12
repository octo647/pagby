<div>
    

    <?php $branchCount = count($branches); ?>
    <script>
        window.dispatchEvent(new CustomEvent('branchesCount', { detail: { count: <?php echo e($branchCount); ?> } }));
    </script>

    <!--[if BLOCK]><![endif]--><?php if($branchCount === 1): ?>
        <div class="font-semibold text-lg text-gray-700">
            <?php echo e($branches[0]['branch_name'] ?? ''); ?>

        </div>
        <script>
            // Informa ao Alpine/Blade pai que já há filial escolhida
            window.dispatchEvent(new CustomEvent('branchChosen', { detail: { branch: "<?php echo e($branches[0]['branch_name'] ?? ''); ?>" } }));
            // Garante que os demais componentes Livewire recebam o evento e estado
            window.__branch_auto_applied__ = window.__branch_auto_applied__ || false;
            if (!window.__branch_auto_applied__) {
                window.__branch_auto_applied__ = true;
                if (typeof window.Livewire !== 'undefined') {
                    // Atualiza o estado do próprio componente e dispara eventos internos
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('chosenBranch', "<?php echo e($branches[0]['branch_name'] ?? ''); ?>");
                }
            }
        </script>
    <?php elseif($branchCount > 1): ?>
        <select wire:model="chosen_branch" name="branch" id="branch" onchange="chosenBranch(this.value)" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option value="" selected>Selecione a filial</option>
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
               <!--[if BLOCK]><![endif]--><?php if(!empty($branch['branch_name'])): ?> 
                    <option value="<?php echo e($branch['branch_name']); ?>"><?php echo e($branch['branch_name']); ?></option>
               <?php endif; ?><!--[if ENDBLOCK]><![endif]-->    
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->       
        </select>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <script>
        function chosenBranch(value) {
            if (!value) {
                console.log("Nenhuma filial selecionada.");
                return;
            }
            console.log("Filial selecionada:", value);
            window.dispatchEvent(new CustomEvent('branchChosen', { detail: { branch: value } }));
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('chosenBranch', value); // Chama o método Livewire diretamente
        }
    </script>
</div><?php /**PATH /home/helder/projetos/pagby/resources/views/livewire/branches.blade.php ENDPATH**/ ?>