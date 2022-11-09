namespace: {module}
actor: Tester
paths:
    tests: tests
    output: tests/_output
    data: tests/_data
    support: tests/_support
    envs: tests/_envs
settings:
    suite_class: \PHPUnit\Framework\TestSuite
    colors: true
    memory_limit: 1024M
    log: true
coverage:
    enabled: true
    whitelist:
        include:
            - 'src/*.php'
