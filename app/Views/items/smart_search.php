
<div id="smart_search" class="smart-search">
  <h2 class="border">Умный поиск</h2>      
  <div class="form-group text-center border">
    <span>Выберите из списка вид комплектующих (обязательно)</span>
    <select name="part_kind"  class="form-control">
        <option></option>
        <?php foreach ($part_kinds as $item) : ?>
            <option value="<?= $item['part_kind'] ?>"><?= $item['for_list'] ?></option>
        <?php endforeach; ?>
    </select>
    <span class="text-danger"></span>
  </div>
  <div class="form-group text-center border">
    <label for="search">Введите модель устройства или наименование детали (по возможности)</label>
    <input type="search" name="search" aria-label="search" class="form-control mt-1 mb-1">                
  </div>
  <div class="form-group border">
    <button  type="submit" class="btn btn-primary stage-1">
      Вперед
    </button>                
  </div> 
</div>