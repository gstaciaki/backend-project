const fetchTasks = async () => {
    return [
        { id: 1, title: 'Arrumar as parada', createdAt: '14/10/2023', finishedAt: '14/12/2023', owners: ['Guilherme Staciaki', 'Pedro Lopes', 'Osvaldo', 'Livia'], priority: 'Alta' },
        { id: 2, title: 'Fazer compras', createdAt: '15/10/2023', finishedAt: '20/10/2023', owners: ['Maria Silva'], priority: 'Média' },
        { id: 3, title: 'Estudar React', createdAt: '16/10/2023', finishedAt: '30/11/2023', owners: ['João Souza'], priority: 'Alta' },
        { id: 4, title: 'Limpar casa', createdAt: '18/10/2023', finishedAt: '19/10/2023', owners: null, priority: 'Baixa' }
    ]
};

export default fetchTasks;
