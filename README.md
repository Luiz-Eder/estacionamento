# Sistema de Controle de Estacionamento - Smart Parking

**Implementação de um sistema de estacionamento inteligente utilizando PHP 8+ e SQLite. Projeto acadêmico focado em demonstrar boas práticas de Clean Code, SOLID e arquitetura modular (Domain-Centric) na prática.**

Poliana Rodriguez - 2000444

Eder Luiz - 1971959

---

## Como executar o projeto  

Clone este repositório ou baixe os arquivos.
git clone https://github.com/Luiz-Eder/estacionamento.git

Copie a pasta para o diretório htdocs do XAMPP:
C:\xampp\htdocs

Inicie o servidor Apache pelo XAMPP.

Acesse no navegador:
[Smart Parking](http://localhost/estacionamento/public/index.php)

---

## Demonstrativo de Funcionamento
#### - Dashboard e Métricas de Negócio

O sistema calcula automaticamente o faturamento e exibe os veículos ativos.


![Dashboard Geral](public/imgs/dashboard.png)

#### - Fluxo de Entrada e Saída (Cálculo Automático)

A aplicação das tarifas (R$ 5/h Carro, R$ 3/h Moto, R$ 10/h Caminhão) é feita via polimorfismo. Ao registrar a saída, o sistema arredonda as horas e gera o recibo.

![Recibo de Saída](public/imgs/recibo_saida.png)

---

## Arquitetura e Decisões Técnicas.

#### - Estrutura de Pastas
- src/Domain: O coração do sistema. Contém as Entidades (Vehicle, Ticket) e Regras de Negócio puras. Não possui dependências de banco de dados ou HTML.

- src/Application: Camada de orquestração. Contém os Casos de Uso (RegisterEntry, GetDashboard) que comandam as ações do sistema.

- src/Infra: Detalhes técnicos. Contém as implementações concretas, como o Repositório SQLite e a Conexão PDO.

#### - Aplicação dos principios SOLID:

- Open/Closed Principle **(OCP):** Utilizamos uma classe base abstrata Vehicle. Os tipos concretos (Car, Motorcycle, Truck) estendem essa base. Isso permite adicionar novos veículos no futuro sem alterar a lógica de cálculo de tarifas existente.

- Liskov Substitution Principle **(LSP):** Qualquer subclasse de Vehicle pode ser utilizada pelo sistema de tickets sem quebrar a aplicação, garantindo polimorfismo seguro.

- Dependency Inversion Principle **(DIP):** Nossos Casos de Uso dependem de uma abstração (TicketRepositoryInterface) e não da classe concreta de banco de dados. Isso reduz o acoplamento e facilita a manutenção.

---
