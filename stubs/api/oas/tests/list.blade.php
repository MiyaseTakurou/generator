    public function test{{ ucfirst($action->getMethod()) }}()
    {

@if( $action->hasParent() )
        $parent = factory(\App\Models\{{ $action->getParentModel() }}::class)->create();
        $variables = [
            '{{ snake_case($action->getParentModel()) }}_id' => $parent->id,
        ];
@else
        $variables = [];
@endif

        $headers = $this->getAuthenticationHeaders();
        $models = factory(\App\Models\{{ $action->getResponse()->getListItem()->getModelName() }}::class, 3)->create($variables);

        $response = $this->action('{{ strtoupper($action->getHttpMethod()) }}', 'Api\{{ $versionNamespace }}\{{ $className }}＠{{ $action->getMethod() }}',
            $variables,
            $input,
            [],
            [],
            $this->transformHeadersToServerVars($headers)
        );
        $this->assertResponseOk();
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(3, count($data['items']));
    }
