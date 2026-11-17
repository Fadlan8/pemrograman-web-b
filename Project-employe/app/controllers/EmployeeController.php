<?php

class EmployeeController extends Controller
{
    private Employee $employeeModel;
    public function __construct()
    {
        $this->employeeModel = new Employee();
    }
    public function index(): void 
    {
        $employees = $this->employeeModel->all();
        $this->view('employee/index', [
            'employees' => $employees,
        ]);
    }
    public function create(): void
    {
        $this->view('employee/create', [
            'errors' => [],
            'old' =>[
                'nip' => '', 'name' => '', 'department' => '', 'position' => '', 'salary' => '',
            ],
        ]);
    }
    public function store(): void
    {
        $input = [
            'nip' => trim($_POST['nip']?? ''),
            'name' => trim($_POST['name']?? ''),
            'department' => trim($_POST['department']?? ''),
            'position' => trim($_POST['position']?? ''),
            'salary' => trim($_POST['salary']?? ''),
        ];
        $errors = $this->validate($input);

        if (!empty($errors)){
            $this->view('employee/create', [
                'errors' => $errors,
                'old' => $input,
            ]);
            return;
        }
        $saved = $this->employeeModel->create([
            'nip' => $input['nip'],
            'name' => $input['name'],
            'department' => $input['department'],
            'position' => $input['position'],
            'salary' => (int) $input['salary'],
        ]);
        if (!$saved) {
            $errors[] = 'Gagal Menyimpan Data Karyawan';
            $this->view('employee/create', [
                'errors' => $errors,
                'old' => $input,
            ]);
            return;
        }
        $this->redirect('/employees');
    }

    public function edit(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('/employees');
        }
        $employee = $this->employeeModel->find($id);
        if (!$employee){
            $this->redirect('/employees');
        }

        //mengubah salary dari decimal databse (misal 6500000.00)ke bilanagan bulat
        $employee['salary'] = (string) ((int) $employee['salary']);
        
        $this->view('employee/edit', [
            'errors' => [],
            'old' => $employee,
        ]);
    }

    public function update(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
             $this->redirect('employees');
        }
        $input = [
            'nip' => trim($_POST['nip']?? ''),
            'name' => trim($_POST['name']?? ''),
            'department' => trim($_POST['department']?? ''),
            'position' => trim($_POST['position']?? ''),
            'salary' => trim($_POST['salary']?? ''),
        ];
        $errors = $this->validate($input, $id);

        if (!empty($errors)){
            $this->view('employee/create', [
                'errors' => $errors,
                'old' => $input,
            ]);
            return;
        }
        $updated = $this->employeeModel->update($id, [
            'nip' => $input['nip'],
            'name' => $input['name'],
            'department' => $input['department'],
            'position' => $input['position'],
            'salary' => (int) $input['salary'],
        ]);
        if (!$updated) {
            $errors[] = 'Gagal Mmperbaharui Data Karyawan';
            $this->view('employee/edit', [
                'errors' => $errors,
                'old' => $input,
            ]);
            return;
        }
        $this->redirect('/employees');

    }
    public function destroy(): void 
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if($id){
            $this->employeeModel->delete($id);
        }
        $this->redirect('/employees');
    }
    private function validate(array $input, ?int $excluedId = null): array
    {
        $errors = [];

        if ($input['nip'] === ''){
            $errors[] = 'NIP wajib diisi.';
        }elseif ($this->employeeModel->nipExists($input['nip'], $excluedId)){
            $errors[] = 'NIP sudah terdaftar, gunakan NIP lain.';
        }
        if ($input['name'] === ''){
            $errors[] = 'Nama karyawan wajib diisi';
        }
        if ($input['department'] === ''){
            $errors[] = 'Department wajib diisi';
        }
        if ($input['position'] === ''){
            $errors[] = 'Posisi wajib diisi';
        }
        if ($input['salary'] === ''){
            $errors[] = 'Salary wajib diisi';
        }elseif (filter_var($input['salary'], FILTER_VALIDATE_INT) === false || (int) $input['salary'] < 0){
             $errors[] = 'Salary harus bilangan bulat >=9.';
        }
        return $errors;
    }
}