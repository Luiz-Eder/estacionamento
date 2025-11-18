# Sistema de Controle de Estacionamento - Smart Parking

#### Sistema de gerenciamento de estacionamento desenvolvido em **PHP 8+**, aplicando princípios de **Arquitetura Limpa**, **SOLID** e **Object Calisthenics**.

poliana rodriguez - 2000444

---

## Demonstrativo de Funcionamento
#### - Dashboard e Métricas de Negócio

O sistema calcula automaticamente o faturamento e exibe os veículos ativos.

#### - Fluxo de Entrada e Saída (Cálculo Automático)

A aplicação das tarifas (R$ 5/h Carro, R$ 3/h Moto, R$ 10/h Caminhão) é feita via polimorfismo. Ao registrar a saída, o sistema arredonda as horas e gera o recibo.

---

## Arquitetura e Decisões Técnicas (Clean Code)

#### - Estrutura Modular e Organização de Pastas
Para respeitar o princípio de Separação de Responsabilidades (SoC), o código foi dividido em camadas distintas, evitando que detalhes de banco de dados contaminem a regra de negócio:

- Domain: Contém as Entidades (Vehicle, Ticket) e Regras de Negócio puras. Não conhece banco de dados nem HTML.

- Application: Contém os Casos de Uso (RegisterEntry, GetDashboard). Orquestra as ações do usuário.

- Infra: Implementações concretas, como o Repositório SQLite e Conexão PDO.

#### - Aplicação do SOLID: OCP e LSP (Domain):

- Criamos uma classe base abstrata Vehicle.

- Os tipos concretos (Car, Motorcycle, Truck) estendem essa base e implementam sua própria estratégia de preço (getHourlyRate).

- Benefício: Se precisarmos adicionar um "Helicóptero" no futuro, criamos uma nova classe sem alterar uma única linha do código de cálculo de tarifas existente (Classe Ticket).
