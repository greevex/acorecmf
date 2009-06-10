<?
class Files extends AModule {
	
	public function __construct(){
		$this->SetName('Файлы');
		
		$this->AddPage('folderPage', 'Файловый менеджер');
		$this->AddEvent('editFiles', 'Редактирование файлов');
		$this->AddEvent('createFiles', 'Создание файлов / каталогов');
		$this->AddEvent('deleteFiles', 'Удаление файлов / каталогов');
		
		parent::__construct('files');
	}
	
	public function ajax_folderPage(){
		return array('content' => 'В разработке!');
	}
	
}
Core::AddModule(new Files());
?>