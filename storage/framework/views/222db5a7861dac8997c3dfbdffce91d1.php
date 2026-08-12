<div>
    <div class="relative overflow-x-auto shadow-md rounded-lg">

        <table class="table-auto w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr><th></th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td wire:key="<?php echo e($index); ?>" class="bg-white border-b hover:bg-gray-50">
                            <?php echo e($interval['funcionario']); ?>

                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
                <tr>
                    <th>seg</th>
                        <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if($editedIndex !== $index): ?>
                                <span  STYLE="font-size:8.0pt">(<?php echo e($interval['seg_int1']); ?>)</span><br>
                                <span  STYLE="font-size:8.0pt">(<?php echo e($interval['seg_int2']); ?>)</span>
                            <?php else: ?>
                                <input type='text'   wire:model.defer='intervals.<?php echo e($index); ?>.seg_int1'>
                                <input type='text'   wire:model.defer='intervals.<?php echo e($index); ?>.seg_int2'>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <th>ter</th>
                        <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if($editedIndex !== $index): ?>
                                <span  STYLE="font-size:8.0pt">(<?php echo e($interval['ter_int1']); ?>)</span><br>
                                <span  STYLE="font-size:8.0pt">(<?php echo e($interval['ter_int2']); ?>)</span>
                            <?php else: ?>
                                <input type='text'  wire:model.defer='intervals.<?php echo e($index); ?>.ter_int1'>
                                <input type='text'  wire:model.defer='intervals.<?php echo e($index); ?>.ter_int2'>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <th>qua</th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td>
                        <?php if($editedIndex !== $index): ?>
                            <span  STYLE="font-size:8.0pt">(<?php echo e($interval['qua_int1']); ?>)</span><br>
                            <span  STYLE="font-size:8.0pt">(<?php echo e($interval['qua_int2']); ?>)</span>
                        <?php else: ?>
                            <input type='text' wire:model.defer='intervals.<?php echo e($index); ?>.qua_int1'>
                            <input type='text' wire:model.defer='intervals.<?php echo e($index); ?>.qua_int2'>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <th>qui</th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                        <?php if($editedIndex !== $index): ?>
                            <span  STYLE="font-size:8.0pt">(<?php echo e($interval['qui_int1']); ?>)</span><br>
                            <span  STYLE="font-size:8.0pt">(<?php echo e($interval['qui_int2']); ?>)</span>
                        <?php else: ?>
                            <input type='text' wire:model.defer='intervals.<?php echo e($index); ?>.qui_int1'>
                            <input type='text' wire:model.defer='intervals.<?php echo e($index); ?>.qui_int2'>
                        <?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <th>sex</th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if($editedIndex !== $index): ?>
                             <span  STYLE="font-size:8.0pt">(<?php echo e($interval['sex_int1']); ?>)</span><br>
                             <span  STYLE="font-size:8.0pt">(<?php echo e($interval['sex_int2']); ?>)</span>
                            <?php else: ?>
                                <input type='text'  wire:model.defer='intervals.<?php echo e($index); ?>.sex_int1'>
                                <input type='text'  wire:model.defer='intervals.<?php echo e($index); ?>.sex_int2'>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <th>sab</th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if($editedIndex !== $index): ?>
                             <span  STYLE="font-size:8.0pt">(<?php echo e($interval['sab_int1']); ?>)</span><br>
                             <span  STYLE="font-size:8.0pt">(<?php echo e($interval['sab_int2']); ?>)</span>
                            <?php else: ?>
                                <input type='text' wire:model.defer='intervals.<?php echo e($index); ?>.sab_int1'>
                                <input type='text' wire:model.defer='intervals.<?php echo e($index); ?>.sab_int2'>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <th>dom</th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td>
                            <?php if($editedIndex !== $index): ?>
                             <span  STYLE="font-size:8.0pt">(<?php echo e($interval['dom_int1']); ?>)</span><br>
                             <span  STYLE="font-size:8.0pt">(<?php echo e($interval['dom_int2']); ?>)</span>
                            <?php else: ?>
                                <input type='text'  wire:model.defer='intervals.<?php echo e($index); ?>.dom_int1'>
                                <input type='text'  wire:model.defer='intervals.<?php echo e($index); ?>.dom_int2'>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <th></th>
                    <?php $__currentLoopData = $intervals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$interval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td>
                        <?php if($editedIndex !== $index): ?>
                            <button type="button" class="btn btn-primary" wire:click.prevent="editMT(<?php echo e($index); ?>)">
                                Editar
                            </button>
                        <?php else: ?>
                            <button  wire:click="saveMT(<?php echo e($index); ?>)">
                                Salvar
                            </button>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>

        </table>
    </div>
</div>

<?php /**PATH /var/www/pagby/resources/views/livewire/intervals.blade.php ENDPATH**/ ?>