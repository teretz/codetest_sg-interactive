def year_with_max_population(people):
    population_changes = [0 for _ in xrange(1900, 2000)]
    for person in people:
        population_changes[person.birth_year - 1900] += 1
        population_changes[person.death_year - 1900] -= 1
    max_population = 0
    max_population_index = 0
    population = 0
    for index, population_change in enumerate(population_changes):
        population += population_change
        if population > max_population:
            max_population = population
            max_population_index = index
    return 1900 + max_population_index