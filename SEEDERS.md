## Sistema de Seeders 


A ideia é ter um **sistema de seeders** que se aproxime ao máximo de uma situação real. Há uma interligação lógica entre as tabelas, dada pela sequência: o cliente escolhe uma filial -> escolhe um funcionário -> escolhe um serviço -> escolhe data e horário do agendamento -> faz o pagamento -> faz avaliação do serviço. Para cumprir esse fluxo, é necessário o seed das tabelas de usuários (users), de funções (roles), de usuários x funções (role_user) e outras mais.
--------------------------------------------------------------------------------
| Tabela                     | Seeder                    | Factory             |
|----------------------------|---------------------------|---------------------|
|  1. roles                  | RolesTableSeeder          |                     |
|  2. users                  | UserTableSeeder           | UserFactory         |
|  3. services               | ServiceTableSeeder        | ServiceFactory      |
|  4. branches               | BranchesTableSeeder       | BranchFactory       |
|  5. role_user              | RoleUserTableSeeder       |                     |
|  6. service_user           | ServiceUserTableSeeder    |                     |
|  7. schedules              | SchedulesTableSeeder      |                     |
|  8. estoque                |                           | EstoqueFactory      |
|  9. plans                  | DefaultPlansSeeder        |                     |
| 10. plan_service           | PlanServiceSeeder         |                     |
| 11. plan_additional_service| PlanServiceSeeder         |                     |
| 12. subscriptions          | SubscriptionsSeeder       |                     |
| 13. appointments           | AppointmentsTableSeeder   | AppointmentFactory  |
| 14. avaliacoes             | AvaliacoesTableSeeder     | AvaliacaoFactory    |
| 15. comanda_servicos       |                           |                     |
| 15. comanda_produtos       |                           |                     |
| 15. comandas               | ComandasSeeder            |                     |
| 16. caixa                  | CaixaSeeder               |                     |
|----------------------------|---------------------------|---------------------|
--------------------------------------------------------------------------------
