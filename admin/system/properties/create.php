<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o id do imóvel a ser atualizado */
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $read = new Read;
    // Verifica se existe id
    if ($id) {
        /**  Faz a consulta para listar os dados do imóvel de acordo com o id */
        $read->ExeRead('ws_properties', "WHERE realty_id = :id", "id={$id}");
        /** Verifica se existe resultado */
        if ($read->getResult()) {
            /** Caso sim, faz uma varredura e armazena na variável */
            $data = array_map('htmlspecialchars', $read->getResult()[0]);
            extract($data);
            unset($data);
        } else {
            /** Caso o id passado não gere resultado, retorna para listagem com uma mensagem */
            Alert::set_flashdata('msg', $lang['update_notfound'], E_USER_NOTICE);
            header('Location: painel.php?exe=properties/index');
            exit();
        }
    } else {
        /** Caso não exista id, o comando é para criar um novo item */
        $create = new Create;
        /** Cria o item e redireciona para página de atualização */
        $create->ExeCreate('ws_properties', array(
                'realty_date' => date('Y-m-d H:i:s')
            )
        );
        header('Location: painel.php?exe=properties/create&id=' . $create->getResult());
        exit();
    }
?>

<div class="container-fluid mb-3">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=properties/index">Imóveis</a></li>
        <li class="breadcrumb-item active">Editar imóvel</li>
    </ol>

    <?php Alert::flashdata('msg'); ?>

    <form class="form" autocomplete="off" action="properties/update/<?= $realty_id; ?>" method="post"
          enctype="multipart/form-data">

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4>Dados do imóvel</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="realty_title">* Título</label>
                            <input type="text" class="form-control" name="realty_title" id="realty_title"
                                   placeholder="Ex: Casa a venda no centro" value="<?= $realty_title; ?>">
                        </div>
                        <div class="form-group">
                            <label for="realty_cover">* Imagem destacada</label>
                            <input type="file" class="form-control load-image" name="realty_cover" id="realty_cover">
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="realty_ref">Referência</label>
                                <input name="realty_ref" id="realty_ref" type="text" class="form-control" readonly="readonly" value="<?= ($realty_ref ? $realty_ref : 'COD' . $realty_id); ?>">
                            </div>
                            <div class="form-group col">
                                <label for="realty_price">Preço</label>
                                <input type="text" class="form-control" name="realty_price" id="realty_price"
                                       placeholder="Ex: 1000"
                                       value="<?= ($realty_price ? number_format($realty_price, 2, ',', '.') : ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="realty_description">* Descrição</label>
                            <textarea class="form-control tiny-editor" name="realty_description" id="realty_description"
                                      placeholder="Descrição do imóvel" rows="5"><?= $realty_description; ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="realty_builtarea">Área útil m²</label>
                                <input type="number" name="realty_builtarea" id="realty_builtarea" placeholder="Ex: 50"
                                       class="form-control" value="<?= $realty_builtarea; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="realty_totalarea">Área total m²</label>
                                <input type="number" class="form-control" name="realty_totalarea" id="realty_totalarea"
                                       placeholder="Ex: 300" value="<?= $realty_totalarea; ?>">
                            </div>
                            <div class="form-group col">
                                <label for="realty_bedrooms">Dormitórios</label>
                                <input type="number" class="form-control" name="realty_bedrooms" id="realty_bedrooms"
                                       value="<?= $realty_bedrooms; ?>">
                            </div>
                            <div class="form-group col">
                                <label for="realty_suites">Suítes</label>
                                <input type="number" class="form-control" name="realty_suites" id="realty_suites"
                                       value="<?= $realty_suites; ?>">
                            </div>
                            <div class="form-group col">
                                <label for="realty_bathrooms">Banheiros</label>
                                <input type="number" class="form-control" name="realty_bathrooms" id="realty_bathrooms"
                                       value="<?= $realty_bathrooms; ?>">
                            </div>
                            <div class="form-group col">
                                <label for="realty_parkings">Vagas</label>
                                <input type="number" class="form-control" name="realty_parkings" id="realty_parkings"
                                       value="<?= $realty_parkings; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="realty_transaction">* Transação</label>
                                <select class="form-control" name="realty_transaction" id="realty_transaction"
                                        required="">
                                    <option value="">Selecione o tipo de transação</option>
                                    <?php foreach (realty_transaction() as $key => $value) { ?>
                                        <option value="<?= $key; ?>" <?= ($realty_transaction == $key ? 'selected=""' : NULL); ?>><?= $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="realty_type">* Tipo do imóvel</label>
                                <select class="form-control" name="realty_type" id="realty_type" required="">
                                    <option value="">Selecione um tipo</option>
                                    <?php foreach (realty_type() as $key => $value) { ?>
                                        <option
                                            <?= ($realty_type == $key ? 'selected=""' : NULL); ?>
                                                value="<?= $key; ?>"><?= $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="realty_finality">* Finalidade</label>
                                <select class="form-control" name="realty_finality" id="realty_finality" required="">
                                    <option value="">Selecione uma finalidade</option>
                                    <?php foreach (realty_finality() as $key => $value) { ?>
                                        <option
                                            <?= ($realty_finality == $key ? 'selected=""' : NULL); ?>
                                                value='<?= $key; ?>'><?= $value; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!--div class="form-group">
                            <label for="realty_particulars">Características</label>
                            <input class="form-control" type="text" name="realty_particulars" id="realty_particulars"
                                   placeholder="Ex: " value="<?= $realty_particulars; ?>">
                        </div-->
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group load-image-src realty_cover">

                            <?php
                                if (Check::file_exists($realty_cover, '../uploads/')) {
                                    echo Check::Image($realty_cover, 'Imagem destacada', 'class="img-fluid"', 294, 159);
                                }
                            ?>

                        </div>
                        <div class="form-group">
                            <label for="images">* Imagens adicionais</label>
                            <input type="file" class="form-control" name="images[]" id="images" multiple>
                        </div>
                        <div class="form-group additional-images">
                            <?php
                                $read->ExeRead('ws_properties_image', 'WHERE realty_id = :id', "id={$realty_id}");
                                if ($read->getResult()) {
                                    foreach ($read->getResult() as $image) {
                                        echo Check::image($image['image'], '', 'id="' . $image['id'] . '"');
                                    }
                                }
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                                $read->FullRead("SELECT realty_city FROM ws_properties GROUP BY realty_city ORDER BY realty_city ASC");
                                if ($read->getResult()) { ?>
                                    <datalist id="realty_city">
                                        <?php foreach ($read->getResult() as $value) { ?>
                                            <option value="<?= $value['realty_city']; ?>"></option>
                                        <?php } ?>
                                    </datalist>
                                <?php } ?>
                            <label for="realty_city">* Cidade</label>
                            <input type="text" class="form-control" name="realty_city" id="realty_city"
                                   placeholder="Ex: São Paulo" list="realty_city" value="<?= $realty_city; ?>"
                                   required="">
                        </div>
                        <div class="form-group">
                            <?php
                                $read->FullRead("SELECT realty_district FROM ws_properties GROUP BY realty_district ORDER BY realty_district ASC");
                                if ($read->getResult()) { ?>
                                    <datalist id="realty_district">
                                        <?php foreach ($read->getResult() as $value) { ?>
                                            <option value="<?= $value['realty_district']; ?>"></option>
                                        <?php } ?>
                                    </datalist>
                                <?php } ?>
                            <label for="realty_district">* Bairro</label>
                            <input type="text" class="form-control" name="realty_district" id="realty_district"
                                   placeholder="Ex: Centro" list="realty_district" value="<?= $realty_district; ?>"
                                   required="">
                        </div>
                        <div class="form-group">
                            <label for="realty_obs">Observações</label>
                            <select name="realty_obs" id="realty_obs" class="form-control">
                                <option value="">Selecione uma observação</option>
                                <?php foreach (realty_obs() as $key => $value) { ?>
                                    <option
                                        <?= ($realty_obs == $key ? 'selected=""' : NULL); ?>value="<?= $key; ?>"><?= $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="realty_date">Data</label>
                            <input type="text" class="form-control date-time" name="realty_date" id="realty_date"
                                   placeholder="99/99/9999 99:99"
                                   value="<?= $realty_date ? date('d/m/Y H:i', strtotime($realty_date)) : date('d/m/Y H:i'); ?>">
                        </div>
                        <div class="i-checks" style="margin-top:2rem;">
                            <input type="checkbox" class="form-control-custom" name="realty_featured"
                                   id="realty_featured" value="1" <?= ($realty_featured == 1 ? 'checked=""' : ''); ?>>
                            <label for="realty_featured">Destaque</label>
                        </div>
                        <div class="i-checks" style="margin-top:2rem;">
                            <input type="checkbox" class="form-control-custom" name="realty_status" id="realty_status"
                                   value="1" <?= ($realty_status == 1 || empty($realty_title) ? 'checked=""' : ''); ?>>
                            <label for="realty_status">Publicar!</label>
                            <button class="btn btn-success btn-load" style="float: right;">
                                <i class="fa fa-save fa-fw"></i> Salvar
                            </button>
                            <div class="icon-load"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>